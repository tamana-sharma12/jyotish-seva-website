/*mobile menu toggle*/
const toggle = document.getElementById("menuToggle");
const navLinks = document.getElementById("navLinks");

if (toggle) {
    toggle.addEventListener("click", () => {
        navLinks.classList.toggle("active"); 
       });
}

/* Animation */
document.querySelectorAll('a[href^="#"]').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        const targetId = this.getAttribute('href');
        const targetSection = document.querySelector(targetId);

        if (targetSection) {
        
            window.scrollTo({
                top: targetSection.offsetTop - 60,
                behavior: 'auto' 
            });

            
            targetSection.classList.remove('fade-in-now');
            void targetSection.offsetWidth; 
            targetSection.classList.add('fade-in-now');
            
            
            if(navLinks) navLinks.classList.remove("active");
        }
    });
});

/* fetch data from db*/
document.addEventListener("DOMContentLoaded", () => {
   fetch('/jyotish-seva/api/get_astrologer_info.php') 

        .then(response => response.json())
        .then(res => {
            if (res.success && res.data) {
                const info = res.data; 

                /* --- A. HERO SECTION DATA --- */
                const hero = document.getElementById('home');
                if (info.bg_img && hero) {
                    // Background Image
                    hero.style.backgroundImage = `linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('${info.bg_img}')`;
                }
                // Hero Title and Tagline 
                if(document.getElementById('hero-heading')) document.getElementById('hero-heading').innerText = info.name || "";
                if(document.getElementById('hero-tagline')) document.getElementById('hero-tagline').innerText = info.tagline || "";

                /* --- B. ABOUT SECTION DATA --- */
                                if(document.getElementById('astrologer-name')) document.getElementById('astrologer-name').innerText = info.name || "";
                if(document.getElementById('astrologer-bio')) document.getElementById('astrologer-bio').innerText = info.bio || "";
                if(document.getElementById('astrologer-img')) document.getElementById('astrologer-img').src = info.photo_url || "";

                /* --- C. EXPERIENCE LIST (Dynamic List) --- */
                
                if(document.getElementById('experience-list') && info.experience_years) {
                    document.getElementById('experience-list').innerHTML = `
                        <li><span class="gold-star">✦</span> Over ${info.experience_years} Years of Experience</li>
                    `;
                }

                /* --- D. SPECIALTIES LIST (Comma Separated to Bullets) --- */
              
                if(document.getElementById('specialties-list') && info.specialties) {
                    const specArray = info.specialties.split(','); 
                                        document.getElementById('specialties-list').innerHTML = specArray
                        .map(item => `<li><span class="gold-star">✦</span> ${item.trim()}</li>`)
                        .join('');
                }
                
                
                if (hero) hero.classList.add('fade-in-now');
            }
        })
        .catch(error => console.error('Error fetching data from DB:', error));
});
/*review section*/
const track = document.getElementById("reviewsTrack");
let reviews = [];
let index = 0;
let slideInterval;

async function fetchReviews() {
    try {
        const res = await fetch('/jyotish-seva/api/get_reviews.php');
        const result = await res.json();
        
        // 1. API ਦੇ ਅੰਦਰੋਂ 'data' ਕੱਢੋ ਅਤੇ ਸਿਰਫ status 1 ਵਾਲੇ ਫਿਲਟਰ ਕਰੋ
        // ਨੋਟ: ਜੇ API status ਨਹੀਂ ਭੇਜ ਰਹੀ ਤਾਂ ਸਿਰਫ result.data ਵਰਤੋ
        reviews = result.data ? result.data.filter(r => r.status == 1 || r.status == undefined) : [];

        if (reviews.length === 0) {
            reviews = Array(5).fill({ reviewer_name: "No Reviews", rating: 0, created_at: "", comment: "No reviews available yet." });
        }

        displayReviews();
        startInfiniteSlide();
    } catch (err) {
        console.error("Error:", err);
        reviews = Array(5).fill({ reviewer_name: "Empty", rating: 0, created_at: "", comment: "Error loading data." });
        displayReviews();
    }
}

function displayReviews() {
    track.innerHTML = "";
    const displayList = [...reviews, ...reviews];

    displayList.forEach(review => {
        const card = document.createElement("div");
        card.classList.add("review-card");

        // ਇੱਥੇ reviewer_name ਅਤੇ created_at ਦੀ ਵਰਤੋਂ ਕਰੋ ਜੋ API ਭੇਜ ਰਹੀ ਹੈ
        card.innerHTML = `
            <div class="review-top">
                <span class="reviewer-name">${review.reviewer_name || 'Anonymous'}</span>
                <span class="review-date">${review.created_at || ''}</span>
            </div>
            <div class="stars">
                ${review.rating > 0 ? "★".repeat(review.rating) + "☆".repeat(5 - review.rating) : "☆☆☆☆☆"}
            </div>
            <p class="review-text">${review.comment}</p>
        `;
        track.appendChild(card);
    });
}


function startInfiniteSlide() {
    if(slideInterval) clearInterval(slideInterval);

    slideInterval = setInterval(() => {
        const card = document.querySelector(".review-card");
        if (!card) return;

        const gap = 30;
        const cardWidth = card.offsetWidth + gap;
        index++;

        track.style.transition = "transform 0.6s cubic-bezier(0.45, 0, 0.55, 1)";
        track.style.transform = `translateX(-${index * cardWidth}px)`;

        // Reset logic for infinite loop
        if (index >= reviews.length) {
            setTimeout(() => {
                track.style.transition = "none";
                index = 0;
                track.style.transform = `translateX(0px)`;
            }, 600);
        }
    }, 3000);
}

// Start everything
fetchReviews();


