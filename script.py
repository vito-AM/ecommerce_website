import os
import json
import csv
import math
from collections import defaultdict

def load_labels_from_csv(csv_path):
    """Charge les labels depuis le fichier CSV"""
    labels_dict = {}
    with open(csv_path, 'r', encoding='utf-8') as csvfile:
        reader = csv.DictReader(csvfile)
        for row in reader:
            # Associe l'ID du fichier avec son label
            file_id = row['file_id']
            label = row['label']
            labels_dict[file_id] = label
    return labels_dict

def process_data(txt_directory, labels_dict):
    """Traite les fichiers texte et leurs labels"""
    texts = []
    labels = []
    
    for filename in os.listdir(txt_directory):
        if filename.endswith(".txt"):
            # Extraire l'ID du fichier du nom (sans l'extension .txt)
            file_id = filename.replace('.txt', '')
            
            # Vérifier si on a un label pour ce fichier
            if file_id in labels_dict:
                with open(os.path.join(txt_directory, filename), 'r', encoding='utf-8') as file:
                    text = file.read().lower()  # Convertir en minuscules
                    texts.append(text)
                    labels.append(labels_dict[file_id])
    
    return texts, labels

def calculate_idf(texts):
    """Calcule l'IDF pour chaque mot"""
    word_doc_count = defaultdict(int)
    total_docs = len(texts)
    
    for text in texts:
        unique_words = set(text.split())
        for word in unique_words:
            word_doc_count[word] += 1
    
    idf = {}
    for word, doc_count in word_doc_count.items():
        idf[word] = math.log(total_docs / doc_count)
    
    return idf

def create_score_map(texts, labels, idf):
    """Crée la carte des scores pour chaque mot"""
    score_map = defaultdict(float)
    
    for text, label in zip(texts, labels):
        words = text.split()
        # Calculer TF pour chaque mot
        word_count = defaultdict(int)
        for word in words:
            word_count[word] += 1
            
        # Calculer et appliquer TF-IDF
        for word, count in word_count.items():
            tf = count / len(words)
            tf_idf = tf * idf.get(word, 0)
            
            # Incrémenter ou décrémenter selon le label
            if label.lower() == "hate":
                score_map[word] -= tf_idf
            else:
                score_map[word] += tf_idf
    
    return dict(score_map)

def save_score_map(score_map, filename):
    """Sauvegarde la carte des scores en JSON"""
    with open(filename, 'w', encoding='utf-8') as f:
        json.dump(score_map, f, ensure_ascii=False, indent=2)

# Programme principal
if __name__ == "__main__":
    # Chemins des fichiers/dossiers
    csv_path = "textes/annotations_metadata.csv"
    txt_directory = "textes/sampled_train"
    output_path = "score_map.json"
    
    print("Chargement des labels depuis le CSV...")
    labels_dict = load_labels_from_csv(csv_path)
    
    print("Traitement des fichiers texte...")
    texts, labels = process_data(txt_directory, labels_dict)
    
    print(f"Nombre de textes traités : {len(texts)}")
    
    print("Calcul de l'IDF...")
    idf = calculate_idf(texts)
    
    print("Création du ScoreMap...")
    score_map = create_score_map(texts, labels, idf)
    
    print("Sauvegarde du ScoreMap...")
    save_score_map(score_map, output_path)
    
    print("Traitement terminé !")