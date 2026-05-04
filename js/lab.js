document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-copy]').forEach(function (button) {
        button.addEventListener('click', function () {
            var value = button.getAttribute('data-copy') || '';
            var notebook = document.querySelector('.notebook-panel textarea');

            if (notebook) {
                notebook.value = notebook.value ? notebook.value + '\n' + value : value;
            }

            if (navigator.clipboard) {
                navigator.clipboard.writeText(value).catch(function () {});
            }

            button.classList.add('copied');
            button.textContent = 'Copied: ' + value;
            window.setTimeout(function () {
                button.classList.remove('copied');
                button.textContent = value;
            }, 1200);
        });
    });

    document.querySelectorAll('.tab-button').forEach(function (button) {
        button.addEventListener('click', function () {
            var tab = button.getAttribute('data-tab');
            document.querySelectorAll('.tab-button').forEach(function (item) {
                item.classList.toggle('active', item === button);
            });
            document.querySelectorAll('.tab-panel').forEach(function (panel) {
                panel.classList.toggle('active', panel.id === 'tab-' + tab);
            });
        });
    });

    document.querySelectorAll('.mark-complete').forEach(function (button) {
        button.addEventListener('click', function () {
            var ring = document.querySelector('.progress-ring');
            if (ring) {
                ring.textContent = '100%';
                ring.classList.add('complete');
            }
            button.textContent = 'Exploited and Noted';
        });
    });

    document.querySelectorAll('.role-switcher button').forEach(function (button) {
        button.addEventListener('click', function () {
            document.querySelectorAll('.role-switcher button').forEach(function (item) {
                item.classList.toggle('active', item === button);
            });
        });
    });
});
