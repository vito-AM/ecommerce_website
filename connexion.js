$(document).ready(function () {
  const form = $("form");

  function showMessage(message, type) {
    // Supprimer l'ancien message s'il existe
    $(".message-container").remove();

    // Créer et insérer le nouveau message
    const messageClass =
      type === "success" ? "success-message" : "error-message";
    const messageHtml = `<div class="message-container ${messageClass}"><p>${message}</p></div>`;
    $("main").prepend(messageHtml);
  }

  form.on("submit", function (e) {
    e.preventDefault();

    const formData = $(this).serialize();

    $.ajax({
      url: "connecter.php",
      type: "POST",
      data: formData,
      dataType: "json",
      success: function (response) {
        if (response.success) {
          showMessage("Connexion réussie! Redirection...", "success");
          setTimeout(() => {
            window.location.href = "index.php";
          }, 1000);
        } else {
          showMessage(response.message, "error");
        }
      },
      error: function () {
        showMessage("Erreur lors de la connexion", "error");
      },
    });
  });
});
