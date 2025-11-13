<?php 
  include_once 'header.php'; 
  $ruta = '../assets/img/intranet/carrusel/'; 
  if(is_dir($ruta)){
    $files=scandir($ruta);            
    $tot_arc=0;            
    foreach($files as $file){
      if($file!=='.' && $file!=='..'){
        $filePath=$ruta.$file;            
        $imageExtensions=['jpg','jpeg','png','gif','bmp','webp'];            
        $fileExtension=strtolower(pathinfo($file, PATHINFO_EXTENSION));            
        if(in_array($fileExtension, $imageExtensions)){
          $tot_arc++;
          $filePath_array[]=$filePath;
        }
      }
    }            
  }else{ echo "La carpeta no existe."; }
?>  
      <style>
        [data-bs-theme="dark"] svg {
          color:rgb(255, 255, 255) !important;
          fill:rgb(255, 255, 255) !important;
        }

        [data-bs-theme="dark"] strong {
          color:rgb(255, 255, 255) !important;
        }

        /* Carrusel mejorado y moderno */
        .carousel {
          border-radius: 20px;
          overflow: hidden;
          box-shadow: 0 12px 48px rgba(0,0,0,0.15);
          margin: 4rem auto 3rem auto;
          position: relative;
          background: var(--bs-body-bg, #212529);
          max-width: 85%;
        }

        .carousel-inner {
          position: relative;
        }

        .carousel-inner::before {
          content: '';
          position: absolute;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          background: linear-gradient(
            to bottom, 
            rgba(0,0,0,0) 0%, 
            rgba(0,0,0,0.05) 50%,
            rgba(0,0,0,0.15) 100%
          );
          z-index: 1;
          pointer-events: none;
          transition: opacity 0.8s ease;
        }


        .carousel-item {
          position: relative;
          overflow: hidden;
          transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.6s ease;
        }

        .carousel-item img {
          object-fit: cover;
          width: 100%;
          height: 100%;
          transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .carousel-item.active img {
          transform: scale(1);
        }

        .carousel-item.active:hover img {
          transform: scale(1.1);
        }

        .carousel-item:not(.active) img {
          transform: scale(1.05);
        }

        /* Efecto de brillo animado sutil en el item activo */
        .carousel-item.active::before {
          content: '';
          position: absolute;
          top: 0;
          left: -100%;
          width: 100%;
          height: 100%;
          background: linear-gradient(
            90deg,
            transparent,
            rgba(255, 255, 255, 0.15),
            transparent
          );
          animation: shimmer 4s infinite;
          z-index: 3;
          pointer-events: none;
        }

        @keyframes shimmer {
          0% {
            left: -100%;
          }
          100% {
            left: 100%;
          }
        }

        /* Indicadores modernos */
        .carousel-indicators {
          margin-bottom: 1.5rem;
          z-index: 3;
        }

        .carousel-indicators [type="button"] {
          width: 12px;
          height: 12px;
          border-radius: 50%;
          margin: 0 6px;
          background-color: rgba(255, 255, 255, 0.5);
          border: 2px solid rgba(255, 255, 255, 0.8);
          transition: all 0.3s ease;
          opacity: 0.7;
        }

        .carousel-indicators [type="button"]:hover {
          opacity: 1;
          transform: scale(1.2);
        }

        .carousel-indicators [type="button"].active {
          width: 40px;
          border-radius: 6px;
          background: linear-gradient(135deg, #64b5f6 0%, #1976d2 100%);
          border-color: rgba(255, 255, 255, 1);
          opacity: 1;
          box-shadow: 0 4px 12px rgba(100, 181, 246, 0.4);
        }

        .carousel-indicators [type="button"].inactive {
          background-color: rgba(255, 255, 255, 0.4);
        }

        /* Controles de navegación modernos */
        .carousel-control-prev,
        .carousel-control-next {
          width: 60px;
          height: 60px;
          top: 50%;
          transform: translateY(-50%);
          opacity: 0;
          transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
          background: rgba(240, 240, 240, 0.85);
          backdrop-filter: blur(10px);
          border-radius: 50%;
          margin: 0 1rem;
          border: 2px solid rgba(180, 180, 180, 0.4);
          box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .carousel:hover .carousel-control-prev,
        .carousel:hover .carousel-control-next {
          opacity: 1;
        }

        .carousel-control-prev.hide-controls,
        .carousel-control-next.hide-controls {
          opacity: 0 !important;
          pointer-events: none;
        }

        .carousel:hover .carousel-control-prev.hide-controls,
        .carousel:hover .carousel-control-next.hide-controls {
          opacity: 0 !important;
        }

        .carousel-control-prev:hover,
        .carousel-control-next:hover {
          background: rgba(250, 250, 250, 0.95);
          transform: translateY(-50%) scale(1.1);
          box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
          border-color: rgba(150, 150, 150, 0.5);
        }

        .carousel-control-prev {
          left: 0;
        }

        .carousel-control-next {
          right: 0;
        }

        .carousel-control-prev-icon,
        .carousel-control-next-icon {
          width: 24px;
          height: 24px;
          background-size: 24px 24px;
          filter: brightness(0) saturate(0%) opacity(0.6);
          transition: transform 0.3s ease;
        }

        .carousel-control-prev:hover .carousel-control-prev-icon,
        .carousel-control-next:hover .carousel-control-next-icon {
          filter: brightness(0) saturate(0%) opacity(0.8);
        }

        /* Estilos para modo oscuro */
        [data-bs-theme="dark"] .carousel-control-prev,
        [data-bs-theme="dark"] .carousel-control-next {
          background: rgba(80, 80, 80, 0.85);
          border: 2px solid rgba(200, 200, 200, 0.4);
          box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        [data-bs-theme="dark"] .carousel-control-prev:hover,
        [data-bs-theme="dark"] .carousel-control-next:hover {
          background: rgba(120, 120, 120, 0.95);
          border-color: rgba(220, 220, 220, 0.5);
          box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
        }

        [data-bs-theme="dark"] .carousel-control-prev-icon,
        [data-bs-theme="dark"] .carousel-control-next-icon {
          filter: brightness(0) invert(1) opacity(0.9);
        }

        [data-bs-theme="dark"] .carousel-control-prev:hover .carousel-control-prev-icon,
        [data-bs-theme="dark"] .carousel-control-next:hover .carousel-control-next-icon {
          filter: brightness(0) invert(1) opacity(1);
        }

        .carousel-control-prev:hover .carousel-control-prev-icon {
          transform: translateX(-2px);
        }

        .carousel-control-next:hover .carousel-control-next-icon {
          transform: translateX(2px);
        }

        /* Responsive */
        @media (max-width: 768px) {
          .carousel {
            max-width: 95%;
          }

          .carousel-control-prev,
          .carousel-control-next {
            width: 45px;
            height: 45px;
            margin: 0 0.5rem;
          }

          .carousel-indicators [type="button"] {
            width: 10px;
            height: 10px;
            margin: 0 4px;
          }

          .carousel-indicators [type="button"].active {
            width: 30px;
          }

        }

        /* Tarjetas modernas */
        .feature-card {
          background: white;
          border-radius: 16px;
          padding: 2rem;
          text-align: center;
          height: 100%;
          transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
          box-shadow: 0 4px 16px rgba(0,0,0,0.08);
          border: 1px solid rgba(0,0,0,0.05);
          position: relative;
          overflow: hidden;
        }

        .feature-card::before {
          content: '';
          position: absolute;
          top: 0;
          left: 0;
          right: 0;
          height: 4px;
          background: linear-gradient(135deg, #64b5f6 0%, #1976d2 100%);
          transform: scaleX(0);
          transition: transform 0.3s ease;
        }

        .feature-card:hover {
          transform: translateY(-8px);
          box-shadow: 0 12px 32px rgba(0,0,0,0.15);
        }

        .feature-card:hover::before {
          transform: scaleX(1);
        }

        [data-bs-theme="dark"] .feature-card {
          background: #212529;
          border-color: rgba(255,255,255,0.1);
        }

        .feature-icon {
          width: 64px;
          height: 64px;
          margin: 0 auto 1.5rem;
          background: linear-gradient(135deg, #64b5f6 0%, #1976d2 100%);
          border-radius: 16px;
          display: flex;
          align-items: center;
          justify-content: center;
          color: white;
          transition: transform 0.3s ease;
        }

        .feature-card:hover .feature-icon {
          transform: scale(1.1) rotate(5deg);
        }

        .feature-title {
          font-size: 1.25rem;
          font-weight: 600;
          margin-bottom: 1rem;
          color: #212529;
        }

        [data-bs-theme="dark"] .feature-title {
          color: #f8f9fa;
        }

        .feature-btn {
          background: linear-gradient(135deg, #64b5f6 0%, #1976d2 100%);
          border: none;
          border-radius: 10px;
          padding: 0.75rem 1.5rem;
          color: white;
          font-weight: 500;
          transition: all 0.3s ease;
          box-shadow: 0 4px 12px rgba(100, 181, 246, 0.3);
          display: inline-flex;
          align-items: center;
          gap: 0.5rem;
        }

        .feature-btn:hover {
          transform: translateY(-2px);
          box-shadow: 0 6px 20px rgba(100, 181, 246, 0.4);
          color: white;
        }

        .feature-btn svg {
          transition: transform 0.3s ease;
        }

        .feature-btn:hover svg {
          transform: translateX(4px);
        }

        .container.marketing {
          padding: 2rem 0;
        }
      </style>
      <main>        
        <div id="myCarousel" class="carousel slide" data-bs-ride="carousel">
          <div class="carousel-indicators">
            <?php                          
              for($i=1;$i<=$tot_arc;$i++){
                if($i==1){
                ?>
                  <button type="button" data-bs-target="#myCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide <?php echo $i; ?>"></button>
                <?php
                }else{
                ?>
                  <button type="button" data-bs-target="#myCarousel" data-bs-slide-to="<?php echo $i-1; ?>" class="inactive" aria-label="Slide <?php echo $i; ?>"></button>
                <?php
                }
              }
            ?>
          </div>
          <div class="carousel-inner ratio ratio-16x9">
            <?php
              $i=0;
              foreach($filePath_array as $f){
                $i++;
            ?>
            <div class="carousel-item <?php if($i==1){ ?> active <?php } ?>">
              <img src="<?php echo $f; ?>" class="img-fluid" aria-hidden="true" focusable="false">
            </div>
            <?php
              }
            ?>
          </div>
          <button class="carousel-control-prev" type="button" data-bs-target="#myCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Anterior</span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#myCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Siguiente</span>
          </button>
        </div>
      
        <div class="container marketing">      
          <!-- Three columns of text below the carousel -->
          <div class="row g-4">
            <div class="col-lg-4">
              <div class="feature-card">
                <div class="feature-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M1.885.511a1.745 1.745 0 0 1 2.61.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.68.68 0 0 0 .178.643l2.457 2.457a.68.68 0 0 0 .644.178l2.189-.547a1.75 1.75 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.6 18.6 0 0 1-7.01-4.42 18.6 18.6 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877zm10.761.135a.5.5 0 0 1 .708 0l2.5 2.5a.5.5 0 0 1 0 .708l-2.5 2.5a.5.5 0 0 1-.708-.708L14.293 4H9.5a.5.5 0 0 1 0-1h4.793l-1.647-1.646a.5.5 0 0 1 0-.708"/>
                  </svg>
                </div>
                <h3 class="feature-title">DIRECTORIO EMPRESARIAL</h3>
                <p class="text-muted mb-4">Consulta la información de contacto de nuestros colaboradores</p>
                <a href="directorio.php">
                  <button type="button" class="feature-btn">
                    <span>Ver Directorio</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                      <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8"/>
                    </svg>
                  </button>
                </a>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="feature-card">
                <div class="feature-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M4.715 6.542 3.343 7.914a3 3 0 1 0 4.243 4.243l1.828-1.829A3 3 0 0 0 8.586 5.5L8 6.086a1 1 0 0 0-.154.199 2 2 0 0 1 .861 3.337L6.88 11.45a2 2 0 1 1-2.83-2.83l.793-.792a4 4 0 0 1-.128-1.287z"/>
                    <path d="M6.586 4.672A3 3 0 0 0 7.414 9.5l.775-.776a2 2 0 0 1-.896-3.346L9.12 3.55a2 2 0 1 1 2.83 2.83l-.793.792c.112.42.155.855.128 1.287l1.372-1.372a3 3 0 1 0-4.243-4.243z"/>
                  </svg>
                </div>
                <h3 class="feature-title">ENLACES A SERVICIOS</h3>
                <p class="text-muted mb-4">Accede a los servicios empresariales disponibles</p>
                <a href="servicios.php">
                  <button type="button" class="feature-btn">
                    <span>Ver Servicios</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                      <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8"/>
                    </svg>
                  </button>
                </a>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="feature-card">
                <div class="feature-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M3 0h6a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2z"/>
                    <path d="M7.5 1a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1zM3 3a1 1 0 0 0-1 1v7a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1H3z"/>
                    <path d="M11.5 2H13a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2h-1.5a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5z"/>
                  </svg>
                </div>
                <h3 class="feature-title">DOCUMENTACIÓN</h3>
                <p class="text-muted mb-4">Explora y descarga documentos corporativos</p>
                <a href="documentos.php">
                  <button type="button" class="feature-btn">
                    <span>Ver Documentos</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                      <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8"/>
                    </svg>
                  </button>
                </a>
              </div>
            </div>
          </div>
        </div>
      </main>

      <script>
        // Ocultar botones del carousel después de 4 segundos al hacer clic
        document.addEventListener('DOMContentLoaded', function() {
          const carousel = document.getElementById('myCarousel');
          if (!carousel) return;

          const prevButton = carousel.querySelector('.carousel-control-prev');
          const nextButton = carousel.querySelector('.carousel-control-next');
          let hideTimeout = null;

          // Función para ocultar los botones
          function hideButtons() {
            if (prevButton) prevButton.classList.add('hide-controls');
            if (nextButton) nextButton.classList.add('hide-controls');
          }

          // Función para mostrar los botones
          function showButtons() {
            if (prevButton) prevButton.classList.remove('hide-controls');
            if (nextButton) nextButton.classList.remove('hide-controls');
          }

          // Función para reiniciar el temporizador
          function resetHideTimer() {
            // Limpiar el temporizador anterior si existe
            if (hideTimeout) {
              clearTimeout(hideTimeout);
            }
            
            // Mostrar los botones
            showButtons();
            
            // Ocultar después de 3 segundos
            hideTimeout = setTimeout(function() {
              hideButtons();
            }, 3000);
          }

          // Detectar clic en botón anterior
          if (prevButton) {
            prevButton.addEventListener('click', function() {
              resetHideTimer();
            });
          }

          // Detectar clic en botón siguiente
          if (nextButton) {
            nextButton.addEventListener('click', function() {
              resetHideTimer();
            });
          }

          // También ocultar cuando el mouse sale del carousel
          carousel.addEventListener('mouseleave', function() {
            resetHideTimer();
          });

          // Mostrar cuando el mouse entra al carousel
          carousel.addEventListener('mouseenter', function() {
            if (hideTimeout) {
              clearTimeout(hideTimeout);
            }
            showButtons();
          });
        });
      </script>

<?php include_once 'footer.php'; ?>