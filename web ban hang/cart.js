document.addEventListener('DOMContentLoaded', function() {
    // Khởi tạo giỏ hàng
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    
    // Lấy các phần tử DOM cần thiết
    const cartIcon = document.querySelector('#hanhd .thanhp:nth-child(2) img');
    const cartModal = document.getElementById('cartModal');
    const paymentModal = document.getElementById('paymentModal');
    const confirmationModal = document.getElementById('confirmationModal');
    const contactSuccessModal = document.getElementById('contactSuccessModal');
    const closeButtons = document.querySelectorAll('.close');
    const cartItemsContainer = document.getElementById('cartItems');
    const cartTotal = document.getElementById('cartTotal');
    const checkoutBtn = document.getElementById('checkoutBtn');
    const paymentForm = document.getElementById('paymentForm');
    const backToShopBtn = document.getElementById('backToShop');
    const backToHomeBtn = document.getElementById('backToHome');
    const contactForm = document.getElementById('contactForm');
    
    // Mở modal giỏ hàng khi click vào icon giỏ hàng
    cartIcon.addEventListener('click', function() {
      updateCartDisplay();
      cartModal.style.display = 'block';
    });
    
    // Đóng modal khi click vào nút đóng
    closeButtons.forEach(button => {
      button.addEventListener('click', function() {
        cartModal.style.display = 'none';
        paymentModal.style.display = 'none';
        confirmationModal.style.display = 'none';
        contactSuccessModal.style.display = 'none';
      });
    });
    
    // Đóng modal khi click bên ngoài modal
    window.addEventListener('click', function(event) {
      if (event.target === cartModal) {
        cartModal.style.display = 'none';
      }
      if (event.target === paymentModal) {
        paymentModal.style.display = 'none';
      }
      if (event.target === confirmationModal) {
        confirmationModal.style.display = 'none';
      }
      if (event.target === contactSuccessModal) {
        contactSuccessModal.style.display = 'none';
      }
    });
    
    // Thêm sự kiện click cho các nút "Mua ngay" trên sản phẩm
    document.querySelectorAll('.item').forEach((item, index) => {
      const addToCartBtn = document.createElement('button');
      addToCartBtn.textContent = 'Thêm vào giỏ';
      addToCartBtn.className = 'add-to-cart';
      item.appendChild(addToCartBtn);
      
      addToCartBtn.addEventListener('click', function() {
        const product = {
          id: index + 1,
          name: item.querySelector('.tensp').textContent,
          price: parseInt(item.querySelector('.gia').textContent.replace(/\./g, '').replace('VNĐ', '')),
          image: item.querySelector('img').src,
          quantity: 1
        };
        
        addToCart(product);
      });
    });
    
    // Hàm thêm sản phẩm vào giỏ hàng
    function addToCart(product) {
      const existingItem = cart.find(item => item.id === product.id);
      
      if (existingItem) {
        existingItem.quantity += 1;
      } else {
        cart.push(product);
      }
      
      saveCart();
      updateCartDisplay();
      
      // Hiển thị thông báo
      alert(`Đã thêm ${product.name} vào giỏ hàng!`);
    }
    
    // Hàm lưu giỏ hàng vào localStorage
    function saveCart() {
      localStorage.setItem('cart', JSON.stringify(cart));
    }
    
    // Hàm cập nhật hiển thị giỏ hàng
    function updateCartDisplay() {
      cartItemsContainer.innerHTML = '';
      
      if (cart.length === 0) {
        cartItemsContainer.innerHTML = '<p>Giỏ hàng của bạn đang trống</p>';
        cartTotal.textContent = 'Tổng cộng: 0 VNĐ';
        return;
      }
      
      let total = 0;
      
      cart.forEach(item => {
        const itemTotal = item.price * item.quantity;
        total += itemTotal;
        
        const cartItemElement = document.createElement('div');
        cartItemElement.className = 'cart-item';
        cartItemElement.innerHTML = `
          <img src="${item.image}" alt="${item.name}">
          <div class="cart-item-info">
            <div>${item.name}</div>
            <div class="cart-item-price">${formatPrice(item.price)} VNĐ</div>
          </div>
          <div class="cart-item-quantity">
            <button class="decrease-quantity" data-id="${item.id}">-</button>
            <input type="text" value="${item.quantity}" readonly>
            <button class="increase-quantity" data-id="${item.id}">+</button>
          </div>
          <span class="remove-item" data-id="${item.id}">Xóa</span>
        `;
        
        cartItemsContainer.appendChild(cartItemElement);
      });
      
      cartTotal.textContent = `Tổng cộng: ${formatPrice(total)} VNĐ`;
      
      // Thêm sự kiện cho các nút tăng/giảm số lượng
      document.querySelectorAll('.decrease-quantity').forEach(button => {
        button.addEventListener('click', function() {
          const id = parseInt(this.getAttribute('data-id'));
          updateQuantity(id, -1);
        });
      });
      
      document.querySelectorAll('.increase-quantity').forEach(button => {
        button.addEventListener('click', function() {
          const id = parseInt(this.getAttribute('data-id'));
          updateQuantity(id, 1);
        });
      });
      
      // Thêm sự kiện cho nút xóa
      document.querySelectorAll('.remove-item').forEach(button => {
        button.addEventListener('click', function() {
          const id = parseInt(this.getAttribute('data-id'));
          removeFromCart(id);
        });
      });
    }
    
    // Hàm cập nhật số lượng sản phẩm
    function updateQuantity(id, change) {
      const item = cart.find(item => item.id === id);
      
      if (item) {
        item.quantity += change;
        
        if (item.quantity < 1) {
          item.quantity = 1;
        }
        
        saveCart();
        updateCartDisplay();
      }
    }
    
    // Hàm xóa sản phẩm khỏi giỏ hàng
    function removeFromCart(id) {
      cart = cart.filter(item => item.id !== id);
      saveCart();
      updateCartDisplay();
    }
    
    // Hàm định dạng giá
    function formatPrice(price) {
      return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }
    
    // Sự kiện khi click vào nút thanh toán
    checkoutBtn.addEventListener('click', function() {
      if (cart.length === 0) {
        alert('Giỏ hàng của bạn đang trống!');
        return;
      }
      
      cartModal.style.display = 'none';
      paymentModal.style.display = 'block';
    });
    
    // Sự kiện khi submit form thanh toán
    paymentForm.addEventListener('submit', function(e) {
      e.preventDefault();
      
      // Lấy thông tin từ form
      const name = document.getElementById('name').value;
      const email = document.getElementById('email').value;
      const address = document.getElementById('address').value;
      const phone = document.getElementById('phone').value;
      
      // Tạo đơn hàng
      const order = {
        customerInfo: { name, email, address, phone },
        items: cart,
        total: cart.reduce((sum, item) => sum + (item.price * item.quantity), 0),
        date: new Date().toISOString()
      };
      
      // Lưu đơn hàng (trong thực tế sẽ gửi đến server)
      let orders = JSON.parse(localStorage.getItem('orders')) || [];
      orders.push(order);
      localStorage.setItem('orders', JSON.stringify(orders));
      
      // Xóa giỏ hàng
      cart = [];
      saveCart();
      
      // Hiển thị modal xác nhận
      paymentModal.style.display = 'none';
      confirmationModal.style.display = 'block';
      
      // Reset form
      paymentForm.reset();
    });
    
    // Sự kiện khi click vào nút quay lại cửa hàng
    backToShopBtn.addEventListener('click', function() {
      confirmationModal.style.display = 'none';
    });
    
    // Sự kiện khi click vào nút quay lại trang chủ
    backToHomeBtn.addEventListener('click', function() {
      contactSuccessModal.style.display = 'none';
    });
    
    // Sự kiện khi submit form liên hệ
    contactForm.addEventListener('submit', function(e) {
      e.preventDefault();
      
      // Lấy thông tin từ form
      const name = document.getElementById('contactName').value;
      const email = document.getElementById('contactEmail').value;
      const phone = document.getElementById('contactPhone').value;
      const message = document.getElementById('contactMessage').value;
      
      // Tạo đối tượng liên hệ (trong thực tế sẽ gửi đến server)
      const contact = {
        name,
        email,
        phone,
        message,
        date: new Date().toISOString()
      };
      
      // Lưu liên hệ vào localStorage
      let contacts = JSON.parse(localStorage.getItem('contacts')) || [];
      contacts.push(contact);
      localStorage.setItem('contacts', JSON.stringify(contacts));
      
      // Hiển thị modal thành công
      contactSuccessModal.style.display = 'block';
      
      // Reset form
      contactForm.reset();
    });
    
    // Smooth scroll cho menu
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function(e) {
        e.preventDefault();
        
        const targetId = this.getAttribute('href');
        const targetElement = document.querySelector(targetId);
        
        if (targetElement) {
          window.scrollTo({
            top: targetElement.offsetTop - 100,
            behavior: 'smooth'
          });
        }
      });
    });
});