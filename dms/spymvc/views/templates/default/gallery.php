<div class="container py-5">
    <h1 class="text-center mb-4">Our Gallery</h1>
    <p class="text-center text-muted mb-5">Explore our collection of beautiful moments</p>

    <div class="row">
        <?php foreach($gallery as $gal): ?>
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100 gallery-item">
                <div class="position-relative overflow-hidden">
                    <img src="<?php echo base_url(); ?>assets/uploads/files/<?php echo $gal['gal_img']; ?>" 
                         class="card-img-top img-fluid" 
                         alt="<?php echo htmlspecialchars($gal['gal_title'] ?? 'Gallery Image'); ?>"
                         data-bs-toggle="modal" 
                         data-bs-target="#galleryModal"
                         data-img="<?php echo base_url(); ?>assets/uploads/files/<?php echo $gal['gal_img']; ?>"
                         data-title="<?php echo htmlspecialchars($gal['gal_title'] ?? ''); ?>">
                    <div class="gallery-overlay d-flex align-items-center justify-content-center">
                        <div class="text-center text-white">
                            <i class="fas fa-search-plus fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top-0 pt-0">
                  
                    <a class="btn btn-primary w-100" href="<?php echo $burl; ?>admin/login">
                        <i class="fas fa-calendar-check me-2"></i>Book Room
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="galleryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img src="" class="img-fluid" id="modalImage">
            </div>
        </div>
    </div>
</div>

<style>
html, body {
    height: 100%;
    margin: 0;
    padding: 0;
}

body {
    padding-top: 56px;
    display: flex;
    flex-direction: column;
}

main {
    flex: 1 0 auto;
}

.gallery-item {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: none;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    display: flex;
    flex-direction: column;
    height: 100%;
}

.gallery-item .card-footer {
    margin-top: auto;
    padding: 1.25rem 1.25rem 0;
    background: transparent;
    border: none;
}

.gallery-item .btn {
    transition: all 0.3s ease;
    font-weight: 500;
    letter-spacing: 0.5px;
}

.gallery-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.15);
}

.gallery-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.gallery-item:hover .gallery-overlay {
    opacity: 1;
}

.card-img-top {
    height: 250px;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.gallery-item:hover .card-img-top {
    transform: scale(1.05);
}

.modal-content {
    background-color: #f8f9fa;
    border: none;
    border-radius: 12px;
}

.modal-header {
    border-bottom: 1px solid #dee2e6;
}

.modal-body {
    padding: 0;
}

#modalImage {
    max-height: 70vh;
    width: auto;
    margin: 0 auto;
    display: block;
}
</style>

<!-- Include Footer -->
<?php $this->load->view('templates/default/footer'); ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('galleryModal');
    if (modal) {
        modal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
                const imgSrc = button.getAttribute('data-img');
                const imgTitle = button.getAttribute('data-title');
                
                const modalTitle = modal.querySelector('.modal-title');
                const modalImage = modal.querySelector('#modalImage');
                
                modalTitle.textContent = imgTitle || '';
                modalImage.src = imgSrc;
                modalImage.alt = imgTitle || 'Gallery Image';
            });
        }
    });
</script>
</div>