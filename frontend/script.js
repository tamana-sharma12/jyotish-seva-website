/*mobile menu toggle*/
const toggle = document.getElementById("menuToggle");
const navLinks = document.getElementById("navLinks");

if (toggle) {
    toggle.addEventListener("click", () => {
        navLinks.classList.toggle("active"); 
       });
}
/// ================= Ultra Smooth Scroll =================
function ultraSmoothScroll(targetElement, duration = 1200) {
  const start = window.pageYOffset;
  const target = targetElement.getBoundingClientRect().top + start - 70; // adjust for navbar
  const distance = target - start;
  let startTime = null;

  function animation(currentTime) {
    if (!startTime) startTime = currentTime;
    const timeElapsed = currentTime - startTime;
    const progress = Math.min(timeElapsed / duration, 1);
    const ease = 0.5 * (1 - Math.cos(Math.PI * progress)); // easeInOutSine
    window.scrollTo(0, start + distance * ease);
    if (timeElapsed < duration) requestAnimationFrame(animation);
  }

  requestAnimationFrame(animation);
}

// ================= Smooth Scroll for All Links =================
document.querySelectorAll('a[href^="#"]').forEach(link => {
  link.addEventListener('click', function(e) {
    const targetId = this.getAttribute('href');
    const targetElement = document.querySelector(targetId);

    if (targetElement) {
      e.preventDefault();
      ultraSmoothScroll(targetElement, 1200);

      // Close mobile menu if open
      const menuToggle = document.getElementById('menuToggle');
      const navLinks = document.getElementById('navLinks');

      if (menuToggle && navLinks && navLinks.classList.contains('active')) {
        navLinks.classList.remove('active');
      }
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
        
      /*only apear review where status =1*/
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

/* Select date and time */

document.addEventListener("DOMContentLoaded", function() {

    const slotsContainer = document.getElementById("slots_container");
    let bookedDates = [];

    // Fetch booked dates
    fetch('/jyotish-seva/api/get_booked_dates.php')
        .then(res => res.json())
        .then(data => {
            bookedDates = data.booked_dates || [];
            initCalendar();
        })
        .catch(() => initCalendar());

    let fp;

    function initCalendar() {
        fp = flatpickr("#datePicker", {
            inline: false, // Calendar popup
            clickOpens: false,
            minDate: "today",
            dateFormat: "Y-m-d",
            disable: bookedDates,

            onDayCreate: function(dObj, dStr, fp, dayElem) {
                const date = dayElem.dateObj;
                if (!date) return;

                const dateStr = date.getFullYear() + "-" +
                    ("0" + (date.getMonth() + 1)).slice(-2) + "-" +
                    ("0" + date.getDate()).slice(-2);

                if (bookedDates.includes(dateStr)) {
                    dayElem.classList.add("booked-date");
                }
            },

            onChange: function(selectedDates, dateStr) {
                document.getElementById("selected_date").value = dateStr;
                document.getElementById("dateText").innerText = dateStr;

                // Auto-fill booking form date
                const formDate = document.getElementById("form_date");
                if (formDate) formDate.value = dateStr;

                loadSlots(dateStr);
            }
        });
    }

    // Open calendar popup on icon click
    document.getElementById("dateTrigger").addEventListener("click", () => {
        fp.open();
    });

    // Open slots popup manually
    document.getElementById("slotsTrigger").addEventListener("click", () => {
        const date = document.getElementById("selected_date").value;

        if (!date) {
            alert("Select date first");
            return;
        }

        document.getElementById("slotsPopup").style.display = "block";
    });

    function loadSlots(date) {
        slotsContainer.innerHTML = "<p class='status-msg'>Loading slots...</p>";

        fetch(`/jyotish-seva/api/get_slots.php?date=${date}`)
            .then(res => res.json())
            .then(data => {
                const slots = data.slots || data.data || [];
                slotsContainer.innerHTML = "";

                if (slots.length === 0) {
                    slotsContainer.innerHTML = "<p class='status-msg'>No slots available</p>";
                    return;
                }

                slots.forEach(slot => {
                    const div = document.createElement("div");
                    div.className = slot.available ? "slot-box" : "slot-box booked";
                    div.innerText = slot.label || slot.time;

                    if (slot.available) {
                        div.onclick = function() {
                            // Set active state
                            document.querySelectorAll(".slot-box").forEach(el => el.classList.remove("active"));
                            div.classList.add("active");

                            // Fill hidden input
                            document.getElementById("slot_id_input").value = slot.id;

                            // Auto-fill booking form slot
                            const formSlot = document.getElementById("form_slot");
                            if (formSlot) formSlot.value = div.innerText;

                            // ✅ Smooth scroll to booking form
                            const formSection = document.getElementById("booking-form-section");
                            if (formSection) {
                                formSection.scrollIntoView({ behavior: "smooth" });
                            }
                        }
                    } else {
                        div.innerText += " (Full)";
                    }

                    slotsContainer.appendChild(div);
                });

                // Open slots popup automatically
                document.getElementById("slotsPopup").style.display = "block";
            })
            .catch(err => {
                console.error("Error loading slots:", err);
                slotsContainer.innerHTML = "<p class='status-msg'>Error loading slots</p>";
            });
    }

});


document.getElementById("bookingForm").addEventListener("submit", async function(e) {
    e.preventDefault();
    const form = this;
    const submitBtn = form.querySelector(".submit-btn");

    // Client-side validation
    let valid = true;
    const name  = form.name.value.trim();
    const email = form.email.value.trim();
    const phone = form.phone.value.trim();
    form.querySelectorAll("input").forEach(inp => inp.classList.remove("error"));

    if(name.length < 3){ form.name.classList.add("error"); valid=false; }
    if(!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)){ form.email.classList.add("error"); valid=false; }
    if(!/^\d{10}$/.test(phone)){ form.phone.classList.add("error"); valid=false; }

    const slotId = document.getElementById("slot_id_input").value;
    const bookingDate = document.getElementById("selected_date").value;
    if(!slotId || !bookingDate){ alert("Please select slot/date."); valid=false; }
    if(!valid){ alert("Please correct the highlighted fields."); return; }

    submitBtn.disabled = true;
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

    try {
        // Create order
        const orderRes = await fetch("/jyotish-seva/api/create_order.php", {
            method: "POST",
            headers: {"Content-Type":"application/json"},
            body: JSON.stringify({
                amount: 500,
                name, email, phone,
                slot_id: slotId,
                booking_date: bookingDate
            })
        });

        const orderData = await orderRes.json();
        if(!orderData.success || !orderData.data?.order_id){ throw new Error(orderData.message||"Order not generated"); }

        const rzp = new Razorpay({
            key: "rzp_test_SVNwp9h4rEXVhF",
            amount: orderData.data.amount * 100,
            currency: "INR",
            name: "Jyotish Seva",
            description: "Consultation Fee",
            order_id: orderData.data.order_id,
            prefill: { name, email, contact: phone },
            handler: async function(response){
                // Verify payment
                const verifyRes = await fetch("/jyotish-seva/api/verify_payment.php", {
                    method: "POST",
                    headers: {"Content-Type":"application/json"},
                    body: JSON.stringify({...response, booking_date: bookingDate, slot_id: slotId, full_name: name, email, phone, dob: form.dob?.value || "", tob: form.tob?.value || "", pob: form.birth_place?.value || "", amount: 500})
                });
                const result = await verifyRes.json();
                if(result.success){
                    alert("Payment Success! Ref: " + result.booking_ref);
                   
                } else {
                    alert("Payment verification failed: " + result.message);
                    resetBtn();
                }
            },
            modal: { ondismiss: () => resetBtn() },
            theme: { color: "#3b1455" }
        });
        rzp.on('payment.failed', function(resp){ alert("Payment failed: "+resp.error.description); resetBtn(); });
        rzp.open();
    } catch(err){ alert("Error: "+err.message); resetBtn(); }

    function resetBtn(){ submitBtn.disabled=false; submitBtn.innerHTML=originalText; }
});

/*rating*/
const stars = document.querySelectorAll(".stars-select i");
let selectedRating = 0;

stars.forEach((star, idx) => {
  star.addEventListener("click", () => {
    selectedRating = idx + 1;

    stars.forEach((s,i) => {
      s.classList.toggle("active", i < selectedRating);
    });

    document.getElementById("ratingMsg").innerText = "";
  });
});

document.getElementById("submitRating").addEventListener("click", () => {
  const name = document.getElementById("userName").value.trim();
  const comment = document.getElementById("userComment").value.trim();

  if(selectedRating === 0){
    document.getElementById("ratingMsg").innerText = "Please select a rating!";
    return;
  }

  if(name === "" || comment === ""){
    document.getElementById("ratingMsg").innerText = "Please enter your name and comment!";
    return;
  }

  document.getElementById("ratingMsg").innerText = "";

  alert(`Thank you, ${name}! You rated ${selectedRating} stars.\nComment: ${comment}`);

  // Reset
  stars.forEach(s => s.classList.remove("active"));
  selectedRating = 0;
  document.getElementById("userName").value = "";
  document.getElementById("userComment").value = "";
});

// Smooth Scroll for Footer Links
document.querySelectorAll('.footer-link').forEach(link => {
  link.addEventListener('click', function(e) {
    e.preventDefault();
    const targetId = this.getAttribute('href').substring(1);
    const targetEl = document.getElementById(targetId);
    if (targetEl) {
      window.scrollTo({
        top: targetEl.offsetTop - 60,
        behavior: 'smooth'
      });
    }
  });
});