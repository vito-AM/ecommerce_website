import os
import csv
import numpy as np
from tensorflow.keras.preprocessing.text import Tokenizer
from tensorflow.keras.preprocessing.sequence import pad_sequences
from tensorflow.keras.models import Sequential
from tensorflow.keras.layers import Embedding, LSTM, Dense, Dropout, Bidirectional, BatchNormalization
from sklearn.neighbors import KNeighborsClassifier
from gensim.models import Word2Vec

def load_all_data(labeled_dir, unlabeled_dir, csv_path):
    """Charge les données labelisées et non labelisées"""
    # Charger les labels depuis le CSV
    labels_dict = {}
    with open(csv_path, 'r', encoding='utf-8') as csvfile:
        reader = csv.DictReader(csvfile)
        for row in reader:
            file_id = row['file_id']
            labels_dict[file_id] = 1 if row['label'].lower() == 'nohate' else 0
    
    # Charger les textes labellisés
    labeled_texts = []
    labels = []
    for filename in os.listdir(labeled_dir):
        if filename.endswith(".txt"):
            file_id = filename.replace('.txt', '')
            if file_id in labels_dict:
                with open(os.path.join(labeled_dir, filename), 'r', encoding='utf-8') as file:
                    text = file.read().lower()
                    labeled_texts.append(text)
                    labels.append(labels_dict[file_id])
    
    # Charger les textes non labellisés
    unlabeled_texts = []
    for filename in os.listdir(unlabeled_dir):
        if filename.endswith(".txt"):
            with open(os.path.join(unlabeled_dir, filename), 'r', encoding='utf-8') as file:
                text = file.read().lower()
                unlabeled_texts.append(text)
    
    print(f"Nombre de textes labellisés : {len(labeled_texts)}")
    print(f"Nombre de textes non labellisés : {len(unlabeled_texts)}")
    print("Distribution des labels :")
    print(f"- noHate : {sum(1 for l in labels if l == 1)}")
    print(f"- Hate : {sum(1 for l in labels if l == 0)}")
    
    return labeled_texts, unlabeled_texts, np.array(labels)

def build_advanced_model(max_words, embedding_dim, max_len, embedding_matrix):
    """Construit un modèle LSTM bidirectionnel plus sophistiqué"""
    model = Sequential([
        # Couche d'embedding initialisée avec Word2Vec
        Embedding(max_words, embedding_dim,
                 weights=[embedding_matrix],
                 input_length=max_len,
                 trainable=True),  # Permettre à l'embedding de s'affiner
        
        # Première couche BiLSTM avec normalisation et dropout
        Bidirectional(LSTM(128, return_sequences=True)),
        BatchNormalization(),
        Dropout(0.4),
        
        # Seconde couche BiLSTM
        Bidirectional(LSTM(64, return_sequences=True)),
        BatchNormalization(),
        Dropout(0.4),
        
        # Troisième couche BiLSTM
        Bidirectional(LSTM(32)),
        BatchNormalization(),
        Dropout(0.4),
        
        # Couches denses pour la classification
        Dense(64, activation='relu'),
        BatchNormalization(),
        Dropout(0.4),
        Dense(32, activation='relu'),
        BatchNormalization(),
        Dense(1, activation='sigmoid')
    ])
    
    # Utiliser une configuration d'optimisation plus sophistiquée
    model.compile(optimizer='adam',
                 loss='binary_crossentropy',
                 metrics=['accuracy'])
    
    return model

def train_models(labeled_texts, unlabeled_texts, labels, max_words=10000, max_len=100, embedding_dim=100):
    """Entraîne les modèles avec une approche améliorée"""
    print("\nPréparation du tokenizer...")
    tokenizer = Tokenizer(num_words=max_words)
    all_texts = labeled_texts + unlabeled_texts
    tokenizer.fit_on_texts(all_texts)
    
    print("Conversion des séquences...")
    labeled_sequences = tokenizer.texts_to_sequences(labeled_texts)
    X_labeled = pad_sequences(labeled_sequences, maxlen=max_len)
    
    # Préparer les données pour Word2Vec
    print("\nEntraînement de Word2Vec sur tous les textes...")
    sentences = [text.split() for text in all_texts]
    w2v_model = Word2Vec(sentences=sentences,
                        vector_size=embedding_dim,
                        window=10,  # Fenêtre plus large pour mieux capturer le contexte
                        min_count=1,
                        workers=4,
                        epochs=20)  # Plus d'époques pour un meilleur apprentissage
    
    # Créer la matrice d'embedding
    print("Création de la matrice d'embedding...")
    embedding_matrix = np.zeros((max_words, embedding_dim))
    for word, i in tokenizer.word_index.items():
        if i >= max_words:
            break
        try:
            embedding_vector = w2v_model.wv[word]
            embedding_matrix[i] = embedding_vector
        except:
            continue
    
    # Construire et entraîner le modèle LSTM
    print("\nEntraînement du modèle LSTM...")
    model = build_advanced_model(max_words, embedding_dim, max_len, embedding_matrix)
    
    # Entraînement avec plus d'époques et validation
    history = model.fit(
        X_labeled,
        labels,
        epochs=10,  # Plus d'époques
        batch_size=32,
        validation_split=0.2,
        verbose=1
    )
    
    # Évaluation
    val_loss, val_accuracy = model.evaluate(X_labeled, labels, verbose=0)
    print(f"\nPerformance sur l'ensemble labellisé :")
    print(f"- Loss : {val_loss:.4f}")
    print(f"- Accuracy : {val_accuracy:.4f}")
    
    # Entraîner KNN sur les features extraites du LSTM
    print("\nEntraînement du KNN...")
    lstm_features = model.predict(X_labeled)
    knn_model = KNeighborsClassifier(n_neighbors=5, weights='distance')
    knn_model.fit(lstm_features, labels)
    
    return model, knn_model, tokenizer, w2v_model

if __name__ == "__main__":
    # Chemins des données
    LABELED_DIR = "textes/sampled_train"
    UNLABELED_DIR = "textes/unlabeled"
    CSV_PATH = "textes/annotations_metadata.csv"
    
    print("Chargement des données...")
    labeled_texts, unlabeled_texts, labels = load_all_data(
        LABELED_DIR, UNLABELED_DIR, CSV_PATH
    )
    
    # Entraîner les modèles
    lstm_model, knn_model, tokenizer, w2v_model = train_models(
        labeled_texts, unlabeled_texts, labels
    )
    
    # Sauvegarder les modèles
    if not os.path.exists('models'):
        os.makedirs('models')
    
    print("\nSauvegarde des modèles...")
    lstm_model.save('models/lstm_model.h5')
    w2v_model.save('models/word2vec.model')
    
    with open('models/tokenizer.json', 'w') as f:
        f.write(tokenizer.to_json())
        
    import joblib
    joblib.dump(knn_model, 'models/knn_model.joblib')
    
    print("Entraînement terminé!")