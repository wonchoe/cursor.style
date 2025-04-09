let fontPicker = null;
let checkbox = null;

// Зберігаємо посилання на активні пікери
const colorPickersMap = new Map();
const fontPickersMap = new Map();
const emojiPickersMap = new Map();

function clearCheckBox() {
    checkbox.addEventListener("change", function () {
        if (!checkbox.checked) {
            console.log('time to clean everything');
        }
    });
}

function initColorPicker() {
    console.log("initColorPicker");

    let isInitializingFontPicker = false;
    let isInitializingEmojiPicker = false;

    const initializePickr = () => {
        const colorPickerInputs = document.querySelectorAll('#effects-container [data-color-picker="false"]');
        colorPickerInputs.forEach(input => {
            if (colorPickersMap.has(input)) return;

            const pickr = Pickr.create({
                el: input,
                theme: 'classic',
                useAsButton: true,
                swatches: [
                    'rgba(244, 67, 54, 1)',
                    'rgba(233, 30, 99, 0.95)',
                    'rgba(156, 39, 176, 0.9)',
                    'rgba(103, 58, 183, 0.85)',
                    'rgba(63, 81, 181, 0.8)',
                    'rgba(33, 150, 243, 0.75)',
                    'rgba(3, 169, 244, 0.7)',
                    'rgba(0, 188, 212, 0.7)',
                    'rgba(0, 150, 136, 0.75)',
                    'rgba(76, 175, 80, 0.8)',
                    'rgba(139, 195, 74, 0.85)',
                    'rgba(205, 220, 57, 0.9)',
                    'rgba(255, 235, 59, 0.95)',
                    'rgba(255, 193, 7, 1)'
                ],
                components: {
                    opacity: true,
                    hue: true,
                    interaction: {
                        hex: true,
                        rgba: true,
                        hsla: true,
                        hsva: true,
                        cmyk: true,
                        input: true
                    }
                }
            });

            pickr.on('changestop', (event, instance) => {
                const color = instance.getColor();
                const hexColor = color.toHEXA().toString();
                input.value = hexColor;
                input.dispatchEvent(new Event('change', { bubbles: true }));
            }).on('swatchselect', (event, instance) => {
                const color = instance.getColor();
                const hexColor = color.toHEXA().toString();
                input.value = hexColor;
                input.dispatchEvent(new Event('change', { bubbles: true }));
            });

            input.setAttribute('data-color-picker', 'true');
            colorPickersMap.set(input, pickr);
        });
    };

    const initializeFontPickr = () => {
        const inputs = document.querySelectorAll('#effects-container [data-font-picker="false"]');
        inputs.forEach(input => {
            if (input.hasAttribute('data-font-initialized') || fontPickersMap.has(input)) return;

            isInitializingFontPicker = true;

            const fontPicker = new FontPicker(input, {
                language: 'en',
                font: input.value,
                defaultSubset: 'latin',
            });

            fontPicker.on('pick', (font) => {
                input.dataset.font = JSON.stringify(font);
                input.dispatchEvent(new Event('change', { bubbles: true }));
            });

            input.setAttribute('data-font-picker', 'true');
            input.setAttribute('data-font-initialized', 'true');

            fontPickersMap.set(input, fontPicker);
            isInitializingFontPicker = false;
        });
    };

    const initializeEmojiPickr = () => {
        const emojiPickerInputs = document.querySelectorAll('#effects-container [data-emoji-picker="false"]');
        emojiPickerInputs.forEach(input => {
            if (input.hasAttribute('data-emoji-initialized') || emojiPickersMap.has(input)) return;

            isInitializingEmojiPicker = true;

            const container = document.createElement("div");
            container.classList.add("emoji-container");
            input.parentNode.insertBefore(container, input);
            container.appendChild(input);

            const picker = document.createElement("emoji-picker");
            picker.classList.add("emoji-picker");
            container.appendChild(picker);

            input.addEventListener("focus", () => {
                picker.style.display = "block";
            });

            picker.addEventListener("emoji-click", event => {
                input.value += event.detail.unicode;
                input.dispatchEvent(new Event('change', { bubbles: true }));
                console.log(event.detail.unicode);
            });

            const handleFocusOut = (event) => {
                if (!input.contains(event.relatedTarget) && !picker.contains(event.relatedTarget)) {
                    picker.style.display = "none";
                }
            };

            input.addEventListener("blur", handleFocusOut);
            picker.addEventListener("blur", handleFocusOut);

            if (input.name !== 'custom_text') {
                input.addEventListener("keydown", event => {
                    if (event.key.length === 1) {
                        event.preventDefault();
                    }
                });
            }

            input.setAttribute('data-emoji-picker', 'true');
            input.setAttribute('data-emoji-initialized', 'true');

            emojiPickersMap.set(input, { picker, container });
            isInitializingEmojiPicker = false;
        });
    };

    function cleanupPickersForNode(node) {
        if (colorPickersMap.has(node)) {
            console.log("Destroying color picker for:", node);
            colorPickersMap.get(node).destroyAndRemove();
            colorPickersMap.delete(node);
        }

        if (fontPickersMap.has(node)) {
            console.log("Destroying font picker for:", node);
            fontPickersMap.get(node).destroy?.(); // if destroy method exists
            fontPickersMap.delete(node);
        }

        if (emojiPickersMap.has(node)) {
            console.log("Destroying emoji picker for:", node);
            const { picker, container } = emojiPickersMap.get(node);
            picker.remove();
            if (container && container.parentNode) {
                container.parentNode.replaceChild(node, container); // unwrap
            }
            emojiPickersMap.delete(node);
        }
    }

    initializePickr();
    initializeFontPickr();
    initializeEmojiPickr();

    const observer = new MutationObserver((mutations) => {
        if (isInitializingEmojiPicker) return;

        mutations.forEach(mutation => {
            // Додавання нових
            mutation.addedNodes.forEach(node => {
                if (node.nodeType !== Node.ELEMENT_NODE) return;

                if (node.getAttribute('data-color-picker') === 'false') initializePickr();
                if (node.getAttribute('data-emoji-picker') === 'false') initializeEmojiPickr();
                if (node.getAttribute('data-font-picker') === 'false') initializeFontPickr();

                node.querySelectorAll('[data-color-picker="false"]').forEach(() => initializePickr());
                node.querySelectorAll('[data-emoji-picker="false"]').forEach(() => initializeEmojiPickr());
                node.querySelectorAll('[data-font-picker="false"]').forEach(() => initializeFontPickr());
            });

            // Видалення
            mutation.removedNodes.forEach(node => {
                if (node.nodeType !== Node.ELEMENT_NODE || node.isConnected) return;

                cleanupPickersForNode(node);

                node.querySelectorAll('input').forEach(child => {
                    cleanupPickersForNode(child);
                });
            });
        });
    });

    observer.observe(document.querySelector('#effects-container'), {
        childList: true,
        subtree: true
    });
}

function initEmojiPicker() {
    document.querySelectorAll(".emoji").forEach(input => {
        const container = document.createElement("div");
        container.classList.add("emoji-container");
        input.parentNode.insertBefore(container, input);
        container.appendChild(input);

        const picker = document.createElement("emoji-picker");
        picker.classList.add("emoji-picker");
        container.appendChild(picker);

        input.addEventListener("focus", () => {
            picker.style.display = "block";
        });

        picker.addEventListener("emoji-click", event => {
            input.value += event.detail.unicode;
            console.log(event.detail.unicode);
        });

        const handleFocusOut = (event) => {
            if (!input.contains(event.relatedTarget) && !picker.contains(event.relatedTarget)) {
                picker.style.display = "none";
            }
        };

        input.addEventListener("blur", handleFocusOut);
        picker.addEventListener("blur", handleFocusOut);

        input.addEventListener("keydown", event => {
            if (event.key.length === 1) {
                event.preventDefault();
            }
        });
    });
}

document.addEventListener("DOMContentLoaded", () => {
    initColorPicker();
    checkbox = document.getElementById("cursoreffect");
    clearCheckBox();
});
