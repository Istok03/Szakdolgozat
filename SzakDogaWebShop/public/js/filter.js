document.addEventListener('DOMContentLoaded', function() {
    const button = document.getElementById('filterButton');
    const panel = document.getElementById('filterPanel');

    if (button && panel) {
        button.addEventListener('click', function() {
            panel.style.display = (panel.style.display === 'block') ? 'none' : 'block';
        });
    }
});

document.getElementById('filterButton').addEventListener('click', () => {
  document.getElementById('filterPanel').classList.toggle('show');
});
