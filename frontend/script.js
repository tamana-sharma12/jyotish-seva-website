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
