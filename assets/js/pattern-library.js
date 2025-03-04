
        jQuery(document).ready(function($) {
            // Gestion du changement de catégorie
            $(".pattern-category").on("click", function() {
                var category = $(this).data("category");

                // Mettre à jour les classes actives
                $(".pattern-category").removeClass("active");
                $(this).addClass("active");

                $(".pattern-category-content").removeClass("active");
                $(".pattern-category-content[data-category=\"" + category + "\"]").addClass("active");
            });

            // Recherche de patterns
            $("#pattern-search").on("keyup", function() {
                var value = $(this).val().toLowerCase();

                $(".pattern-item").each(function() {
                    var title = $(this).find(".pattern-title").text().toLowerCase();
                    if (title.indexOf(value) > -1) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });

            // Visualiser le pattern en grand
            $(".pattern-view").on("click", function() {
                var iframeId = $(this).data("iframe");
                var $iframe = $("#" + iframeId);
                var content = $iframe.attr("srcdoc");

                // Créer une modal
                var $modal = $("<div>", {
                    class: "pattern-modal",
                    css: {
                        position: "fixed",
                        top: 0,
                        left: 0,
                        width: "100%",
                        height: "100%",
                        background: "rgba(0,0,0,0.8)",
                        zIndex: 9999,
                        display: "flex",
                        justifyContent: "center",
                        alignItems: "center"
                    }
                });

                // Créer un conteneur pour l'iframe
                var $modalContent = $("<div>", {
                    css: {
                        width: "80%",
                        height: "80%",
                        background: "white",
                        position: "relative",
                        borderRadius: "4px",
                        overflow: "hidden"
                    }
                });

                // Créer un bouton de fermeture
                var $closeBtn = $("<button>", {
                    html: "×",
                    css: {
                        position: "absolute",
                        top: "10px",
                        right: "10px",
                        background: "black",
                        color: "white",
                        border: "none",
                        borderRadius: "50%",
                        width: "30px",
                        height: "30px",
                        fontSize: "20px",
                        cursor: "pointer",
                        zIndex: 1
                    }
                });

                // Créer un nouvel iframe
                var $newIframe = $("<iframe>", {
                    srcdoc: content,
                    css: {
                        width: "100%",
                        height: "100%",
                        border: "none"
                    }
                });

                // Assembler la modal
                $modalContent.append($closeBtn);
                $modalContent.append($newIframe);
                $modal.append($modalContent);
                $("body").append($modal);

                // Gestion de la fermeture
                $closeBtn.on("click", function() {
                    $modal.remove();
                });

                $modal.on("click", function(e) {
                    if (e.target === this) {
                        $modal.remove();
                    }
                });
            });
        });
        