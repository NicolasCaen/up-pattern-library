jQuery(document).ready(function($) {
    // Fonction pour redimensionner les éléments
    function resizeElements() {
        $('.pattern-item').each(function() {
            // Forcer un rafraîchissement du contenu de l'élément
            var height = $(this).height();
            $(this).height(height);
        });
    }

    // Exécuter au chargement de la page
    resizeElements();

    // Exécuter lors du redimensionnement de la fenêtre
    $(window).on('resize', function() {
        resizeElements();
    });

    // Supprimer les styles inline qui définissent la hauteur à 0px
    $('.pattern-item').each(function() {
        if ($(this).attr('style') && $(this).attr('style').indexOf('height: 0px') !== -1) {
            $(this).removeAttr('style');
        }
    });

    // Filtrage par catégorie
    $('.pattern-category').on('click', function() {
        const category = $(this).data('category');
        
        // Mettre à jour la catégorie active
        $('.pattern-category').removeClass('active');
        $(this).addClass('active');
        
        // Afficher les patterns de la catégorie sélectionnée
        $('.pattern-category-content').removeClass('active');
        $(`.pattern-category-content[data-category="${category}"]`).addClass('active');
        
        // Redimensionner les éléments après le changement de catégorie
        setTimeout(function() {
            // Forcer le recalcul des dimensions pour les patterns visibles
            $(`.pattern-category-content[data-category="${category}"] .pattern-content`).each(function() {
                $(this).css('display', 'block');
                var width = $(this).width();
                $(this).css('width', width);
            });
            resizeElements();
        }, 100);

        // Supprimer à nouveau les styles inline après changement de catégorie
        $('.pattern-item').each(function() {
            if ($(this).attr('style') && $(this).attr('style').indexOf('height: 0px') !== -1) {
                $(this).removeAttr('style');
            }
        });
    });
    
    // Recherche de patterns
    $('#pattern-search').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        
        $('.pattern-item').each(function() {
            const title = $(this).find('.pattern-title').text().toLowerCase();
            if (title.includes(searchTerm)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
        
        // Redimensionner les éléments après la recherche
        setTimeout(resizeElements, 100);
    });
    
    // Affichage du pattern en plein écran
    $('.pattern-view').on('click', function() {
        const patternId = $(this).data('pattern-id');
        const patternTitle = $(this).data('pattern-title');
        const patternHtml = $(this).data('pattern-html');
        
        if (!patternHtml) {
            console.error(`Pattern HTML not found for ID: ${patternId}`);
            return;
        }
        
        // Créer la modal
        const modal = $(`
            <div class="pattern-modal">
                <div class="pattern-modal-content">
                    <div class="pattern-modal-header">
                        <h3>${patternTitle}</h3>
                        <button class="pattern-modal-close">
                            <span class="dashicons dashicons-no-alt"></span>
                        </button>
                    </div>
                    <div class="pattern-modal-body">
                        <div class="pattern-modal-preview">
                            <div class="pattern-modal-container">
                                <div class="pattern-modal-frame">
                                    <div class="pattern-content">${patternHtml}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `);
        
        // Ajouter la modal au body
        $('body').append(modal);
        
        // Fermer la modal
        modal.find('.pattern-modal-close').on('click', function() {
            modal.remove();
        });
        
        // Fermer la modal en cliquant en dehors
        modal.on('click', function(e) {
            if ($(e.target).hasClass('pattern-modal')) {
                modal.remove();
            }
        });
    });
});