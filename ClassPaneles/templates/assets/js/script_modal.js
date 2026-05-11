document.addEventListener("DOMContentLoaded", function () {

    // =========================
    // MODAL CREAR ESTUDIANTE
    // =========================
    const studentModal = document.getElementById("createUserModal");
    const openStudentBtn = document.getElementById("openCreateUserModal");
    const studentCancelBtn = studentModal?.querySelector(".cancel-button");
    const uploadBtn = document.getElementById("uploadPhotoBtn");
    const photoInput = document.getElementById("photoInput");
    const profileImage = document.getElementById("profileImage");

    if (openStudentBtn) {
        openStudentBtn.addEventListener("click", function (e) {
            e.preventDefault();
            studentModal.style.display = "flex";
        });
    }

    if (studentCancelBtn) {
        studentCancelBtn.addEventListener("click", function () {
            studentModal.style.display = "none";
        });
    }

    if (uploadBtn) {
        uploadBtn.addEventListener("click", function (e) {
            e.preventDefault();
            photoInput.click();
        });
    }

    if (photoInput) {
        photoInput.addEventListener("change", function (e) {
            const file = e.target.files[0];

            if (file) {
                const reader = new FileReader();

                reader.onload = function (event) {
                    profileImage.src = event.target.result;
                };

                reader.readAsDataURL(file);
            }
        });
    }


    // =========================
    // MODAL CREAR CURSO
    // =========================
    const courseModal = document.getElementById("createCourseModal");
    const openCourseBtn = document.getElementById("openCreateCourseModal");
    const closeCourseBtn = document.getElementById("closeCreateCourseModal");

    if (openCourseBtn) {
        openCourseBtn.addEventListener("click", function (e) {
            e.preventDefault();
            console.log("Botón Crear Curso presionado");
            courseModal.style.display = "flex";
        });
    }

    if (closeCourseBtn) {
        closeCourseBtn.addEventListener("click", function () {
            courseModal.style.display = "none";
        });
    }


    // =========================
    // CERRAR MODALES AL HACER CLICK FUERA
    // =========================
 // POR ESTO:
    window.addEventListener("click", function (event) {
        if (event.target === studentModal) studentModal.style.display = "none";
        if (event.target === courseModal)  courseModal.style.display = "none";
        if (event.target === viewCoursesModal) viewCoursesModal.style.display = "none"; // ← agregado
    });
/*VER CURSOS*/
const openViewCoursesModal = document.getElementById('openViewCoursesModal');
const viewCoursesModal = document.getElementById('viewCoursesModal');
const closeViewCoursesModal = document.getElementById('closeViewCoursesModal');
const searchCoursesModal = document.getElementById('searchCoursesModal');
const coursesTableBody = document.getElementById('coursesTableBody');

// Abrir modal
if (openViewCoursesModal) {
    openViewCoursesModal.addEventListener('click', (e) => {
        e.preventDefault();
        viewCoursesModal.style.display = 'flex';
    });
}

// Cerrar modal
if (closeViewCoursesModal) {
    closeViewCoursesModal.addEventListener('click', () => {
        viewCoursesModal.style.display = 'none';
    });
}
/*
// Cerrar al hacer click fuera
window.addEventListener('click', (e) => {
    if (e.target === viewCoursesModal) {
        viewCoursesModal.style.display = 'none';
    }
});*/

// Buscador en tiempo real
if (searchCoursesModal && coursesTableBody) {
    searchCoursesModal.addEventListener('input', function () {
        const filter = this.value.toLowerCase().trim();
        const rows = coursesTableBody.querySelectorAll('tr');

        rows.forEach(row => {
            if (row.cells.length < 2) return;
            const courseName = row.cells[1].textContent.toLowerCase().trim();
            row.style.display = courseName.includes(filter) ? '' : 'none';
        });
    });
}

});

// =========================
// BUSCADOR DE CURSOS
// =========================
const searchInput = document.getElementById("courseSearch");
const resultsContainer = document.getElementById("courseResults");
const selectedContainer = document.getElementById("selectedCourses");

let selectedCourses = [];

if (searchInput) {

    searchInput.addEventListener("input", function () {
        const value = this.value.toLowerCase();
        resultsContainer.innerHTML = "";

        if (!value) return;

        const filtrados = cursosDisponibles.filter(curso =>
            curso.nombre_curso.toLowerCase().includes(value)
        );

        filtrados.forEach(curso => {

            if (selectedCourses.find(c => c.id == curso.id)) return;

            const div = document.createElement("div");
            div.classList.add("course-item");
            div.textContent = curso.nombre_curso;

            div.onclick = () => addCourse(curso);

            resultsContainer.appendChild(div);
        });
    });

    function addCourse(curso) {
        selectedCourses.push(curso);
        renderSelected();
        searchInput.value = "";
        resultsContainer.innerHTML = "";
    }

    function renderSelected() {
        selectedContainer.innerHTML = "";

        selectedCourses.forEach(curso => {
            const tag = document.createElement("div");
            tag.classList.add("course-tag");

            tag.innerHTML = `
                ${curso.nombre_curso}
                <span class="remove">×</span>
                <input type="hidden" name="cursos[]" value="${curso.id}">
            `;

            tag.querySelector(".remove").onclick = () => {
                selectedCourses = selectedCourses.filter(c => c.id !== curso.id);
                renderSelected();
            };

            selectedContainer.appendChild(tag);
        });
    }
}