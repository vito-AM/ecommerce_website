// chat.js
document.addEventListener("DOMContentLoaded", function () {
  // Création de la fenêtre de chat
  const chatContainer = document.createElement("div");
  chatContainer.id = "chat-container";

  // Zone des messages
  const messagesArea = document.createElement("div");
  messagesArea.id = "chat-messages";

  // Formulaire d'envoi
  const form = document.createElement("form");
  form.id = "chat-form";
  form.innerHTML = `
    <input type="hidden" name="csrf_token" id="csrf_token" value="${CHAT_CSRF_TOKEN}">
    <input type="text" id="chat-input" maxlength="256">
    <button type="submit">Envoyer</button>
`;

  // Assemblage des éléments
  chatContainer.appendChild(messagesArea);
  chatContainer.appendChild(form);
  document.body.appendChild(chatContainer);

  // Fonction pour charger les messages
  function loadMessages() {
    fetch("./messages.php")
      .then((response) => response.json())
      .then((data) => {
        if (data.success && data.messages) {
          const html = data.messages
            .map(
              (msg) =>
                `<div class="message">
                  <strong>${msg.prenom} ${msg.nom}</strong> dit '${msg.message}'
                </div>`
            )
            .join("");
          messagesArea.innerHTML = html;
        }
      })
      .catch(() => {
        messagesArea.innerHTML =
          '<div class="error">Erreur de chargement</div>';
      });
  }

  function showError(message) {
    const errorDiv = document.createElement("div");
    errorDiv.className = "chat-error";
    errorDiv.textContent = message;
    errorDiv.style.cssText =
      "background-color: #ff6b6b; color: white; padding: 10px; margin: 10px; border-radius: 4px; text-align: center;";

    // Insérer l'erreur avant le formulaire
    form.parentNode.insertBefore(errorDiv, form);

    // Faire disparaître l'erreur après 3 secondes
    setTimeout(() => {
      errorDiv.style.opacity = "0";
      setTimeout(() => errorDiv.remove(), 300);
    }, 5000);
  }

  // Gestion de l'envoi des messages
  form.addEventListener("submit", function (e) {
    e.preventDefault();
    const input = document.getElementById("chat-input");
    const csrfTokenInput = document.getElementById("csrf_token"); // Récupérer le champ du token CSRF
    const message = input.value.trim();

    if (message) {
      const formData = new FormData();
      formData.append("message", message);
      formData.append("csrf_token", csrfTokenInput.value); // Ajouter le token CSRF

      fetch("./messages.php", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            input.value = "";
            loadMessages();
          } else {
            // Afficher le message d'erreur
            showError(data.error || "Erreur lors de l'envoi du message");
          }
        })
        .catch(() => {
          showError("Erreur de connexion au serveur");
        });
    }
  });

  // Chargement initial et rafraîchissement périodique
  loadMessages();
  setInterval(loadMessages, 500);
});
