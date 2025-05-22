<?php include('layouts/header.php'); ?>
<!-- Font Awesome 4.7.0 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">

<section id="contact" class="container my-5 py-5">
    <div class="text-center mb-4">
        <h2 class="fw-bold">Contactează-ne</h2>
        <p class="text-muted">Suntem aici pentru orice întrebare sau sugestie.</p>
        <hr class="w-25 mx-auto">
    </div>
    <div class="row">
        <!-- Contact Info -->
    <div class="col-md-6 mb-4">
    <div class="mb-3 d-flex align-items-start">
        <i class="fa fa-phone fa-2x icons me-3" aria-hidden="true"></i>
        <div>
        <h5 class="mb-1">Telefon</h5>
        <p class="mb-0">+40 712 345 678</p>
        </div>
    </div>
    <div class="mb-3 d-flex align-items-start">
        <i class="fa fa-envelope fa-2x icons me-3" aria-hidden="true"></i>
        <div>
        <h5 class="mb-1">Email</h5>
        <p class="mb-0">infocompanyemail2@gmail.com</p>
        </div>
    </div>
    <div class="mb-3 d-flex align-items-start">
        <i class="fa fa-map-marker fa-2x icons me-3" aria-hidden="true"></i>
        <div>
        <h5 class="mb-1">Adresă</h5>
        <p class="mb-0">Cluj-Napoca, România</p>
        </div>
    </div>
    <div class="mb-3 d-flex align-items-start">
        <i class="fa fa-clock-o fa-2x icons me-3" aria-hidden="true"></i>
        <div>
        <h5 class="mb-1">Program</h5>
        <p class="mb-0">Luni - Vineri: 09:00 - 18:00</p>
        </div>
    </div>
    </div>


        <!-- Map + Form -->
        <div class="col-md-6">
            <iframe class="mb-4 rounded-3 shadow" 
                src="https://www.google.com/maps/embed?pb=!1m10!1m8!1m3!1d43723.411962680475!2d23.5779215!3d46.7705484!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sro!2sro!4v1747575050589!5m2!1sro!2sro" 
                width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy">
            </iframe>

            <!-- Contact Form -->
            <form action="send_message.php" method="post">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="name" name="name" placeholder="Numele tău" required>
                    <label for="name">Nume complet</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="email" class="form-control" id="email" name="email" placeholder="nume@exemplu.com" required>
                    <label for="email">Adresa de email</label>
                </div>
                <div class="form-floating mb-3">
                    <textarea class="form-control" placeholder="Scrie mesajul tău aici" id="message" name="message" style="height: 120px;" required></textarea>
                    <label for="message">Mesajul tău</label>
                </div>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fa fa-paper-plane"></i> Trimite mesajul
                </button>
            </form>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <?php include('layouts/footer.php'); ?>
</body>
</html>
