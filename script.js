let cart = JSON.parse(localStorage.getItem("cart")) || [];

function addToCart(name, price) {
  const existing = cart.find(item => item.name === name);
  if (existing) {
    existing.quantity += 1;
  } else {
    cart.push({ name, price, quantity: 1 });
  }
  localStorage.setItem("cart", JSON.stringify(cart));
  alert(name + " تمت إضافته إلى السلة ✅");
}

function renderCart() {
  const container = document.querySelector(".cart-items");
  if (!container) return;

  container.innerHTML = "";
  if (cart.length === 0) {
    container.innerHTML = "<p>لا توجد عناصر في السلة 🥲</p>";
    return;
  }

  let total = 0;
  cart.forEach((item, index) => {
    const div = document.createElement("div");
    div.innerHTML = `
      <h4>${item.name}</h4>
      <p>السعر: ${item.price} دج</p>
      <p>
        الكمية:
        <button onclick="updateQuantity(${index}, -1)">➖</button>
        ${item.quantity}
        <button onclick="updateQuantity(${index}, 1)">➕</button>
      </p>
      <hr>
    `;
    container.appendChild(div);
    total += item.price * item.quantity;
  });

  document.getElementById("total").textContent = total + " دج";
}

function updateQuantity(index, change) {
  cart[index].quantity += change;
  if (cart[index].quantity <= 0) {
    cart.splice(index, 1);
  }
  localStorage.setItem("cart", JSON.stringify(cart));
  renderCart();
}

function checkout() {
  window.location.href = "checkout.html";
}

function submitForm(event) {
  event.preventDefault();

  const form = document.querySelector(".checkout-form");

  const data = {
    full_name: form.querySelectorAll("input")[0].value,
    address: form.querySelectorAll("input")[1].value,
    phone: form.querySelector("input[type='tel']").value,
    payment_method: form.querySelector("select").value,
    cart: cart
  };

  if (!data.full_name || !data.address || !data.phone || !data.payment_method || data.cart.length === 0) {
    alert("يرجى ملء جميع المعلومات وعدم ترك السلة فارغة");
    return;
  }

  fetch("submit_order.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json"
    },
    body: JSON.stringify(data)
  })
    .then(res => res.json())
    .then(res => {
      alert(res.message);
      localStorage.removeItem("cart");
      window.location.href = "index.html";
    })
    .catch(err => {
      console.error(err);
      alert("حدث خطأ أثناء إرسال الطلب");
    });
}
