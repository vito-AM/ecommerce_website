from tensorflow.keras.models import load_model
from tensorflow.keras.preprocessing.sequence import pad_sequences
from tensorflow.keras.preprocessing.text import tokenizer_from_json
from gensim.models import Word2Vec
import numpy as np
import joblib
import json

class MessageAnalyzer:
    def __init__(self, max_words=10000, max_len=100, embedding_dim=100):
        self.max_words = max_words
        self.max_len = max_len
        self.embedding_dim = embedding_dim
        self.lstm_model = None
        self.knn_model = None
        self.tokenizer = None
        self.w2v_model = None
    
    def load_models(self, models_dir='models'):
        """Loads all required models"""
        # Load LSTM
        self.lstm_model = load_model(f'{models_dir}/lstm_model.h5')
        
        # Load KNN
        self.knn_model = joblib.load(f'{models_dir}/knn_model.joblib')
        
        # Load tokenizer
        with open(f'{models_dir}/tokenizer.json', 'r') as f:
            tokenizer_json = f.read()
            self.tokenizer = tokenizer_from_json(tokenizer_json)
        
        # Load Word2Vec
        self.w2v_model = Word2Vec.load(f'{models_dir}/word2vec.model')
    
    def detect_negation_context(self, text):
        """Detects negation contexts with English negation markers"""
        # English negation markers
        negation_words = {
            'not', 'no', 'never', 'none', 'nothing', 'nowhere', 'neither',
            'cant', 'cannot', "can't", "won't", "wouldn't", "shouldn't",
            "isn't", "aren't", "wasn't", "weren't", "haven't", "hasn't",
            "hadn't", "doesn't", "don't", "didn't", 'without', 'nobody',
            'never', 'none', 'nor', 'nothing', 'nowhere'
        }
        
        # Scope-ending markers
        scope_enders = {'.', ',', 'but', 'however', 'nevertheless', 'yet', 
                       'although', 'though', ';', '!', '?'}
        
        words = text.lower().split()
        negation_contexts = []
        current_context = []
        in_negation = False
        
        for i, word in enumerate(words):
            if word in negation_words:
                in_negation = True
            
            if in_negation:
                current_context.append(word)
                
                # Check if next word exists and is a scope ender
                if i < len(words) - 1:
                    next_word = words[i + 1]
                    if next_word in scope_enders:
                        if current_context:
                            negation_contexts.append(current_context)
                            current_context = []
                        in_negation = False
                
                # Limit negation scope to 6 words
                if len(current_context) >= 6:
                    negation_contexts.append(current_context)
                    current_context = []
                    in_negation = False
        
        if current_context:
            negation_contexts.append(current_context)
        
        return negation_contexts

    def get_semantic_orientation(self, word):
        """Determines semantic orientation of a word using Word2Vec"""
        try:
            # English reference words
            positive_refs = ['good', 'nice', 'excellent', 'positive', 'wonderful']
            negative_refs = ['bad', 'terrible', 'horrible', 'negative', 'awful']
            
            if word not in self.w2v_model.wv:
                return 0
            
            # Calculate average similarity with positive and negative words
            pos_similarity = np.mean([
                self.w2v_model.wv.similarity(word, ref)
                for ref in positive_refs
                if ref in self.w2v_model.wv
            ])
            
            neg_similarity = np.mean([
                self.w2v_model.wv.similarity(word, ref)
                for ref in negative_refs
                if ref in self.w2v_model.wv
            ])
            
            return pos_similarity - neg_similarity
        except:
            return 0

    def analyze_message(self, message):
        """Analyzes a message with advanced negation handling"""
        try:
            # Detect negation contexts
            negation_contexts = self.detect_negation_context(message)
            has_negation = len(negation_contexts) > 0
            
            # Preprocess message
            sequence = self.tokenizer.texts_to_sequences([message])
            padded = pad_sequences(sequence, maxlen=self.max_len)
            
            # Get LSTM predictions
            lstm_features = self.lstm_model.predict(padded, verbose=0)
            lstm_pred = lstm_features[0][0]
            
            # Get KNN prediction
            knn_pred = self.knn_model.predict(lstm_features)[0]
            
            # Base score (70% LSTM, 30% KNN)
            base_score = (0.7 * float(lstm_pred > 0.5) + 
                         0.3 * float(knn_pred))
            
            # If negation is detected, calculate semantic score
            if has_negation:
                semantic_scores = []
                for context in negation_contexts:
                    for word in context:
                        semantic_scores.append(self.get_semantic_orientation(word))
                
                # If we have semantic scores, use them to adjust the inversion
                if semantic_scores:
                    avg_semantic_score = np.mean(semantic_scores)
                    # Inversion modulated by semantic score
                    final_score = 1 - (base_score * abs(avg_semantic_score))
                else:
                    final_score = 1 - base_score
            else:
                final_score = base_score
            
            return final_score > 0.5
            
        except Exception as e:
            print(f"Error during analysis: {str(e)}")
            return True  # Accept message by default in case of error