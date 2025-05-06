function setupImageDrop(dropId, inputId, offsetXId, offsetYId, crossXId, crossYId, sliderXId, sliderYId) {
    const drop = document.getElementById(dropId);
    const input = document.getElementById(inputId);
    const offsetXInput = document.getElementById(offsetXId);
    const offsetYInput = document.getElementById(offsetYId);
    const crossX = document.getElementById(crossXId);
    const crossY = document.getElementById(crossYId);
    const xSlider = document.getElementById(sliderXId);
    const ySlider = document.getElementById(sliderYId);    
    let imageLoaded = false;

    drop.addEventListener('click', (e) => {
        if (!imageLoaded) {
            input.click();
            return;
        }
    
        const rect = drop.getBoundingClientRect();
        const rawX = Math.round(e.clientX - rect.left);
        const rawY = Math.round(e.clientY - rect.top);
    
        // Масштаб: 300px -> 128px
        const scale = 128 / 300;
        const scaledX = Math.round(rawX * scale);
        const scaledY = Math.round(rawY * scale);
    
        // Перехрестя залишаємо по raw координатах (для візуалу)
        crossX.style.left = `${rawX}px`;
        crossY.style.top = `${rawY}px`;
    
        // А в value та слайдери — scaled (як для 128×128)
        offsetXInput.value = scaledX;
        offsetYInput.value = scaledY;
        xSlider.value = scaledX;
        ySlider.value = scaledY;
    });
    

    input.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (event) => {
                drop.style.backgroundImage = `url('${event.target.result}')`;
                drop.textContent = '';
                imageLoaded = true;
            
                if (inputId === 'c_file') {
                    const nameField = document.getElementById('name');
                    const fileName = file.name.split('.')[0]; // без розширення
                    const prefix = 'cursor-';
                    if (fileName.startsWith(prefix)) {
                        const stripped = fileName.slice(prefix.length);
                        nameField.value = stripped;
                    }
                }
            };
            reader.readAsDataURL(file);
        }
    });
}


document.addEventListener('DOMContentLoaded', () => {
    setupImageDrop(
        'cursorDrop',     // dropId
        'c_file',         // inputId
        'offsetX',        // offsetX input field
        'offsetY',        // offsetY input field
        'cursorCrossX',   // вертикальна лінія
        'cursorCrossY',   // горизонтальна лінія
        'offsetX_slider', // горизонтальний слайдер
        'offsetY_slider'  // вертикальний слайдер
    );

    setupImageDrop(
        'pointerDrop',
        'p_file',
        'offsetX_p',
        'offsetY_p',
        'pointerCrossX',
        'pointerCrossY',
        'offsetX_p_slider',
        'offsetY_p_slider'
    );

    const catSelect = document.getElementById('cat_select');

    // Load selected value from localStorage
    const savedCat = localStorage.getItem('selected_cat_id');
    if (savedCat && catSelect) {
        catSelect.value = savedCat;
    }
    

    // Save to localStorage on change
    if (catSelect) {
        catSelect.addEventListener('change', function () {
            localStorage.setItem('selected_cat_id', this.value);
        });
    }    

    document.getElementById('formId').addEventListener('submit', function(e) {
        const name = document.getElementById('name').value.trim();
        const ox = parseInt(document.getElementById('offsetX').value);
        const oy = parseInt(document.getElementById('offsetY').value);
        const opx = parseInt(document.getElementById('offsetX_p').value);
        const opy = parseInt(document.getElementById('offsetY_p').value);
    
        if (!name || (ox === 0 && oy === 0 && opx === 0 && opy === 0)) {
            e.preventDefault();
            alert('Please provide a name and set coordinates before submitting.');
        }
    });  
    

});

