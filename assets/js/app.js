document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('form[data-validate]').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            let valid = true;
            form.querySelectorAll('[required]').forEach(function (input) {
                if (!input.value || (input.type === 'number' && input.value === '')) {
                    valid = false;
                    input.classList.add('invalid');
                } else {
                    input.classList.remove('invalid');
                }
            });
            if (!valid) {
                event.preventDefault();
                alert('Silakan lengkapi semua field yang wajib diisi.');
            }
        });
    });
});
