<!-- Page Header -->
<header class="bg-light py-5 mb-5">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="<?php echo $burl; ?>" class="text-decoration-none">
                                <i class="fas fa-home me-1"></i> Home
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <?php echo $webpage['web_page_name']; ?>
                        </li>
                    </ol>
                </nav>
                <h1 class="display-5 fw-bold mt-3 mb-0">
                    <?php echo $webpage['web_page_name']; ?>
                </h1>
                <div class="border-bottom border-3 border-primary d-inline-block mb-3" style="width: 80px;"></div>
            </div>
        </div>
    </div>
</header>

<!-- Main Content -->
<main class="mb-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="page-content bg-white p-4 rounded-3 shadow-sm">
                    <?php echo $webpage['web_page_content']; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Call to Action -->
<?php if(empty($this->session->userdata('user_id')) && $webpage['web_page_name'] != 'Contact'): ?>
<section class="bg-primary text-white py-5">
    <div class="container text-center">
        <h3 class="mb-4">Ready to Book Your Stay?</h3>
        <p class="lead mb-4">Experience our exceptional hospitality and comfort</p>
        <a href="<?php echo $burl; ?>admin/login" class="btn btn-light btn-lg px-4 me-2">
            <i class="fas fa-sign-in-alt me-2"></i>Login to Book
        </a>
        <a href="<?php echo $burl; ?>register" class="btn btn-outline-light btn-lg px-4">
            <i class="fas fa-user-plus me-2"></i>Register
        </a>
    </div>
</section>
<?php endif; ?>

<style>
.page-content {
    line-height: 1.8;
    font-size: 1.05rem;
}

.page-content img {
    max-width: 100%;
    height: auto;
    margin: 1.5rem 0;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.page-content h2, 
.page-content h3, 
.page-content h4 {
    margin-top: 2rem;
    margin-bottom: 1rem;
    color: #2c3e50;
}

.page-content p {
    margin-bottom: 1.2rem;
}

.breadcrumb {
    background: transparent;
    padding: 0.5rem 0;
    margin-bottom: 0;
}

.breadcrumb-item a {
    color: #6c757d;
    transition: color 0.2s ease;
}

.breadcrumb-item a:hover {
    color: #0d6efd;
    text-decoration: none;
}

.breadcrumb-item + .breadcrumb-item::before {
    content: '›';
    padding: 0 0.5rem;
}
</style>