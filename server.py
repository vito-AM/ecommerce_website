from flask import Flask, request, jsonify
from flask_cors import CORS
import logging
from message_analyzer import MessageAnalyzer

# Configuration du logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

app = Flask(__name__)
CORS(app, resources={
    r"/analyze": {
        "origins": ["http://127.0.0.1", "http://localhost"],
        "methods": ["POST"],
        "allow_headers": ["Content-Type"]
    }
})

# Initialisation de l'analyseur
try:
    analyzer = MessageAnalyzer()
    analyzer.load_models('models')
    logger.info("Modèles chargés avec succès")
except Exception as e:
    logger.error(f"Erreur lors du chargement des modèles : {str(e)}")
    raise

@app.route('/analyze', methods=['POST'])
def analyze_message():
    try:
        # Vérification de la requête
        if not request.is_json:
            return jsonify({
                'success': False,
                'error': 'Content-Type must be application/json'
            }), 400
            
        data = request.get_json()
        logger.info(f"Requête reçue : {data}")
        
        if not data or 'message' not in data:
            return jsonify({
                'success': False,
                'error': 'Message manquant'
            }), 400
        
        message = data['message'].strip()
        if not message:
            return jsonify({
                'success': False,
                'error': 'Message vide'
            }), 400
            
        # Analyse du message
        try:
            result = analyzer.analyze_message(message)
            logger.info(f"Résultat de l'analyse : {result}")
            
            # Si result est un tuple, on prend juste le booléen
            is_appropriate = result[0] if isinstance(result, tuple) else result
            
            return jsonify({
                'success': True,
                'is_appropriate': bool(is_appropriate),
                'message': message
            })
            
        except Exception as e:
            logger.error(f"Erreur lors de l'analyse : {str(e)}")
            raise
        
    except Exception as e:
        logger.error(f"Erreur globale : {str(e)}")
        return jsonify({
            'success': False, 
            'error': "Erreur lors de l'analyse du message"
        }), 500

@app.errorhandler(Exception)
def handle_error(error):
    logger.error(f"Erreur non gérée : {str(error)}")
    return jsonify({
        'success': False,
        'error': "Une erreur interne est survenue"
    }), 500

if __name__ == '__main__':
    # En production, mettre debug=False
    app.run(host='127.0.0.1', port=5000, debug=True, use_reloader=False)