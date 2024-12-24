const modal = document.getElementById("modal");
const openModal = document.getElementById("openModal");
const closeModal = document.getElementById("closeModal");

openModal.addEventListener("click", () => {
    modal.classList.remove("hidden");
    modal.classList.add('flex');
});

closeModal.addEventListener("click", () => {
    modal.classList.add("hidden");
});

// Fermer le modal si l'utilisateur clique en dehors du modal
window.addEventListener("click", (e) => {
    if (e.target === modal) {
        modal.classList.add("hidden");
    }
});