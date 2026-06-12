// Sample listings data
const listings = [
    {
        id: 1,
        title: 'Kost Nyaman di Depok',
        price: 'Rp 1.500.000/bulan',
        location: 'Depok, Jawa Barat',
        image: 'https://via.placeholder.com/300x200?text=Kost+Depok'
    },
    {
        id: 2,
        title: 'Kost Strategis di Jakarta',
        price: 'Rp 2.000.000/bulan',
        location: 'Jakarta Pusat',
        image: 'https://via.placeholder.com/300x200?text=Kost+Jakarta'
    },
    {
        id: 3,
        title: 'Kost Terjangkau di Bogor',
        price: 'Rp 1.200.000/bulan',
        location: 'Bogor, Jawa Barat',
        image: 'https://via.placeholder.com/300x200?text=Kost+Bogor'
    }
];

// Load listings when page loads
document.addEventListener('DOMContentLoaded', () => {
    loadListings();
    setupEventListeners();
});

function loadListings() {
    const container = document.getElementById('listings-container');

    listings.forEach(listing => {
        const card = document.createElement('div');
        card.className = 'listing-card';
        card.innerHTML = `
            <img src="${listing.image}" alt="${listing.title}">
            <div class="listing-card-content">
                <h3>${listing.title}</h3>
                <p><strong>${listing.price}</strong></p>
                <p>${listing.location}</p>
            </div>
        `;
        container.appendChild(card);
    });
}

function setupEventListeners() {
    // Add smooth scroll for navigation links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
}

// Fetch listings from API (optional for PHP backend)
async function fetchListingsFromAPI() {
    try {
        const response = await fetch('../php/api/listings.php');
        if (!response.ok) throw new Error('Failed to fetch listings');
        return await response.json();
    } catch (error) {
        console.error('Error fetching listings:', error);
        return listings; // Fallback to static data
    }
}
