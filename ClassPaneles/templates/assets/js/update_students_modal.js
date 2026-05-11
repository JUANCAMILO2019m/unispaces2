document.querySelectorAll('.openUpdateStudentsModal').forEach(btn => {
    btn.addEventListener('click', e => {
        e.preventDefault();

        const id = btn.dataset.id;

        fetch(`../../php/get_students.php?id=${id}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('update_id').value = data.id;
                document.getElementById('update_nombre').value = data.nombre_completo;
                document.getElementById('update_correo').value = data.correo;
                document.getElementById('update_usuario').value = data.identificacion;
                document.getElementById('correo_original').value = data.correo;

                const img = document.getElementById('updateProfileImage');
                img.src = data.imagen ? `../../uploads/estudiantes/${data.imagen}` 
                : '../../assets/images/photo.jpg';

                selectedCoursesUpdate = data.cursos || [];
                renderSelectedCoursesUpdate();

                // actualizar ACTION del form con el ID
                document.getElementById('updateStudentsModal').action =
                    `update_students.php?id=${id}`;

                document.getElementById('updateStudentsModal').style.display = 'flex';
            });
    });
});

document.getElementById('closeUpdateModal').onclick = () => {
    document.getElementById('updateStudentsModal').style.display = 'none';
};

document.getElementById('uploadPhotoBtnUpdate').addEventListener('click', e => {
    e.preventDefault();
    document.getElementById('photoInputUpdate').click();
});
document.getElementById('photoInputUpdate').addEventListener('change', e => {
    const file = e.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = () => {
        document.getElementById('updateProfileImage').src = reader.result;
    };
    reader.readAsDataURL(file);
});

/*CURSOS*/
// Variables
const updateCourseSearch = document.getElementById('updateCourseSearch');
const updateCourseResults = document.getElementById('updateCourseResults');
const updateSelectedCourses = document.getElementById('updateSelectedCourses');

let selectedCoursesUpdate = [];

// Función para mostrar cursos disponibles
function filterCoursesUpdate(query) {
    updateCourseResults.innerHTML = '';
    const filtered = cursosDisponibles.filter(c => c.nombre_curso.toLowerCase().includes(query.toLowerCase()));
    filtered.forEach(curso => {
        const div = document.createElement('div');
        div.textContent = curso.nombre_curso;
        div.classList.add('course-item');
        div.addEventListener('click', () => {
            if (!selectedCoursesUpdate.some(sc => sc.id === curso.id)) {
                selectedCoursesUpdate.push(curso);
                renderSelectedCoursesUpdate();
            }
            updateCourseResults.innerHTML = '';
            updateCourseSearch.value = '';
        });
        updateCourseResults.appendChild(div);
    });
}

// Función para renderizar cursos seleccionados
function renderSelectedCoursesUpdate() {
    updateSelectedCourses.innerHTML = '';
    selectedCoursesUpdate.forEach(curso => {
        const span = document.createElement('span');
        span.classList.add('selected-course');
        span.textContent = curso.nombre_curso;
        const removeBtn = document.createElement('i');
        removeBtn.classList.add('fas', 'fa-times');
        removeBtn.addEventListener('click', () => {
            selectedCoursesUpdate = selectedCoursesUpdate.filter(c => c.id !== curso.id);
            renderSelectedCoursesUpdate();
        });
        span.appendChild(removeBtn);
        updateSelectedCourses.appendChild(span);
    });
}

// Filtrar mientras se escribe
updateCourseSearch.addEventListener('input', e => filterCoursesUpdate(e.target.value));

document.getElementById('updateStudentsForm').addEventListener('submit', e => {
    document.getElementById('cursos_seleccionados').value =
        JSON.stringify(selectedCoursesUpdate.map(c => c.id));
});