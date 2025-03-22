from message_analyzer import MessageAnalyzer
from colorama import Fore, Style, init

def test_analyzer():
    # Initialiser l'analyseur
    analyzer = MessageAnalyzer()
    
    # Charger les modèles
    analyzer.load_models()

    # Initialiser colorama
    init(autoreset=True)

    # Messages de test
    test_messages = [
        'Most white girls are taught and brainwashed to love black guys, only way is to properly educate them and show them what savages they truly are.',
        'I have a flyer at home that has the symbols to look for so once I return home I can get that info out 2 you .',
        'But you get alot of American White nationalists or Nazis claiming to want all non-White descendants out of America but America is not a native White continent or country .',
    ]
    
    # Tester chaque message
    print("\nTesting message analysis:")
    print("-" * 50)
    
    for message in test_messages:
        result = analyzer.analyze_message(message)
        negation_contexts = analyzer.detect_negation_context(message)

        # Afficher les résultats avec des couleurs
        result_color = Fore.GREEN if result else Fore.RED

        print(f"\nMessage: '{message}'")
        print(f"Is appropriate: {result_color}{result}{Style.RESET_ALL}")
        if negation_contexts:
            print(f"Negation contexts detected: {Fore.YELLOW}{negation_contexts}{Style.RESET_ALL}")
        print("-" * 50)

if __name__ == "__main__":
    test_analyzer()
