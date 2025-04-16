document.addEventListener('DOMContentLoaded', function() {
    const typeFilter = document.getElementById('event-type-filter');
    const monthFilter = document.getElementById('event-month-filter');
    const eventCards = document.querySelectorAll('.event-card');

    function filterEvents() {
        const selectedType = typeFilter.value;
        const selectedMonth = monthFilter.value;

        eventCards.forEach(card => {
            const cardType = card.dataset.type;
            const cardMonth = card.querySelector('.month').textContent;
            
            const typeMatch = selectedType === 'all' || cardType === selectedType;
            const monthMatch = selectedMonth === 'all' || getMonthNumber(cardMonth) === selectedMonth;

            if (typeMatch && monthMatch) {
                card.style.display = 'flex';
                // Animation d'apparition
                card.style.opacity = '0';
                setTimeout(() => {
                    card.style.opacity = '1';
                }, 50);
            } else {
                card.style.display = 'none';
            }
        });
    }

    function getMonthNumber(monthStr) {
        const months = {
            'JAN': '01', 'FEV': '02', 'MAR': '03', 'AVR': '04',
            'MAI': '05', 'JUN': '06', 'JUL': '07', 'AOU': '08',
            'SEP': '09', 'OCT': '10', 'NOV': '11', 'DEC': '12'
        };
        return months[monthStr.toUpperCase()];
    }

    // Ajouter les écouteurs d'événements pour les filtres
    typeFilter.addEventListener('change', filterEvents);
    monthFilter.addEventListener('change', filterEvents);

    // Animation initiale des cartes
    eventCards.forEach(card => {
        card.style.opacity = '0';
        setTimeout(() => {
            card.style.opacity = '1';
        }, 50);
    });
}); 