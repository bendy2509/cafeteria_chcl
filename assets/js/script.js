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
