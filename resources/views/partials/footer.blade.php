<footer class="bg-dark text-white py-5 mt-auto">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4">
                <h5 class="mb-3">About LaundryGo</h5>
                <p>Professional laundry service at your doorstep. We pick up, clean, and deliver your laundry with utmost care.</p>
                <div class="mt-3">
                    <a href="#" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-white me-3"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-white"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>
            
            <div class="col-md-2 mb-4">
                <h5 class="mb-3">Quick Links</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="{{ route('home') }}" class="text-white-50 text-decoration-none">Home</a></li>
                    <li class="mb-2"><a href="{{ route('services') }}" class="text-white-50 text-decoration-none">Services</a></li>
                    <li class="mb-2"><a href="{{ route('pricing') }}" class="text-white-50 text-decoration-none">Pricing</a></li>
                    <li class="mb-2"><a href="{{ route('how-it-works') }}" class="text-white-50 text-decoration-none">How It Works</a></li>
                    <li class="mb-2"><a href="{{ route('contact') }}" class="text-white-50 text-decoration-none">Contact</a></li>
                </ul>
            </div>
            
            <div class="col-md-3 mb-4">
                <h5 class="mb-3">Customer Service</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">FAQs</a></li>
                    <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Terms & Conditions</a></li>
                    <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Privacy Policy</a></li>
                    <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Refund Policy</a></li>
                </ul>
            </div>
            
            <div class="col-md-3 mb-4">
                <h5 class="mb-3">Contact Info</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="fas fa-map-marker-alt me-2"></i> 123 Laundry Street, City</li>
                    <li class="mb-2"><i class="fas fa-phone me-2"></i> +1 234 567 8900</li>
                    <li class="mb-2"><i class="fas fa-envelope me-2"></i> support@laundrygo.com</li>
                    <li class="mb-2"><i class="fas fa-clock me-2"></i> 24/7 Customer Support</li>
                </ul>
            </div>
        </div>
        
        <hr class="my-3 bg-light">
        
        <div class="row">
            <div class="col-md-12 text-center">
                <p class="mb-0">&copy; {{ date('Y') }} LaundryGo. All rights reserved.</p>
            </div>
        </div>
    </div>
</footer>