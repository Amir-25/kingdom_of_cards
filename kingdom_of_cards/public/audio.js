document.addEventListener("DOMContentLoaded", function () {
    const audio = document.getElementById("audio-player");
    const volumeSlider = document.getElementById("volume");

    // Play audio
    audio.volume = 0.5; // Volume par défaut
    audio.play().catch(error => console.log("Autoplay bloqué par le navigateur :", error));

    // Modifier le volume
    volumeSlider.addEventListener("input", (event) => {
        audio.volume = event.target.value;
      });
});




