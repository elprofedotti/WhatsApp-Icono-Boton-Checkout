<?php
/**
 * Plugin Name: WhatsApp Icono & Boton Checkout
 * Description: Este plugin permite a tus clientes finalizar la compra de sus productos a través de WhatsApp. Añade un botón "Finalizar compra por WhatsApp" en la página del carrito de WooCommerce y muestra un ícono de WhatsApp en la página, ambos con enlaces a un chat de WhatsApp que contiene detalles del carrito, como nombre del producto, cantidad, precio, subtotal y el total. El ícono de WhatsApp también puede mostrar un mensaje personalizado encima del ícono. Las posiciones del ícono y el mensaje personalizado en la pantalla son personalizables, y también es posible configurar un retraso para su aparición.
 * Version: 2.0
 * Author: ElProfeDotti
 * Author URI: https://www.elprofedotti.com.ar
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: woocommerce-whatsapp-checkout
 * Domain Path: /languages
 */

// Crear una página de opciones para que el usuario pueda agregar su número de teléfono y configurar otras opciones
add_action('admin_menu', 'icono_de_whatsapp_menu');

function icono_de_whatsapp_menu() {
    add_options_page('Plugin de WhatsApp', 'Plugin de WhatsApp', 'manage_options', 'mi-plugin-de-whatsapp', 'mi_plugin_de_whatsapp_opciones');
}

function mi_plugin_de_whatsapp_opciones() {
    if (!current_user_can('manage_options')) {
        wp_die(__('No tienes suficientes permisos para acceder a esta página.'));
    }
    ?>
    <div class="wrap">
        <h1>Mi Plugin de WhatsApp</h1>
        <form method="post" action="options.php">
            <?php settings_fields('mi-plugin-de-whatsapp'); ?>
            <?php do_settings_sections('mi-plugin-de-whatsapp'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Número de teléfono:</th>
                    <td><input type="text" name="mi_plugin_de_whatsapp_telefono" value="<?php echo esc_attr(get_option('mi_plugin_de_whatsapp_telefono')); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Retraso (segundos):</th>
                    <td><input type="number" name="mi_plugin_de_whatsapp_retraso" value="<?php echo esc_attr(get_option('mi_plugin_de_whatsapp_retraso', 0)); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Posición del ícono:</th>
                    <td>
                        <select name="mi_plugin_de_whatsapp_posicion">
                            <option value="left" <?php selected(get_option('mi_plugin_de_whatsapp_posicion'), 'left'); ?>>Izquierda</option>
                            <option value="center" <?php selected(get_option('mi_plugin_de_whatsapp_posicion'), 'center'); ?>>Centro</option>
                            <option value="right" <?php selected(get_option('mi_plugin_de_whatsapp_posicion'), 'right'); ?>>Derecha</option>
                        </select>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Mensaje flotante:</th>
                    <td><input type="text" name="mi_plugin_de_whatsapp_mensaje" value="<?php echo esc_attr(get_option('mi_plugin_de_whatsapp_mensaje')); ?>" /></td>
                </tr>
            </table>
			<h2>Configuración del Botón del Carrito</h2>
            <table class="form-table">
                <tr>
					<th scope="row">Mostrar el botón en la página del carrito:</th>
					<td>
						<input type="checkbox" name="mi_plugin_de_whatsapp_boton_carrito" value="1" <?php checked(get_option('mi_plugin_de_whatsapp_boton_carrito'), 1); ?> />
						<span class="description">Marcar para habilitar el botón "Finalizar compra por WhatsApp" en el carrito de compras.</span>
					</td>
				</tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Registrar opciones para guardar la configuración del usuario
add_action('admin_init', 'mi_plugin_de_whatsapp_registrar_opcion');

function mi_plugin_de_whatsapp_registrar_opcion() {
    //register_setting('mi-plugin-de-whatsapp', 'mi_plugin_de_whatsapp_telefono');
	register_setting('mi-plugin-de-whatsapp', 'mi_plugin_de_whatsapp_telefono', 'mi_plugin_de_whatsapp_sanitize_telefono');
    register_setting('mi-plugin-de-whatsapp', 'mi_plugin_de_whatsapp_retraso');
    register_setting('mi-plugin-de-whatsapp', 'mi_plugin_de_whatsapp_posicion');
    register_setting('mi-plugin-de-whatsapp', 'mi_plugin_de_whatsapp_mensaje');
	register_setting('mi-plugin-de-whatsapp', 'mi_plugin_de_whatsapp_boton_carrito');
}

// Función para sanitizar el número de teléfono ingresado
function mi_plugin_de_whatsapp_sanitize_telefono($telefono) {
    $telefono = preg_replace('/[^0-9+]/', '', $telefono); // Eliminar caracteres no numéricos y permitir el signo más

    // Puedes agregar aquí más validaciones si es necesario

    return $telefono;
}

// Mostrar el ícono de WhatsApp en la página con las opciones configuradas
add_action('wp_footer', 'mi_plugin_de_whatsapp_mostrar_icono');

function mi_plugin_de_whatsapp_mostrar_icono() {
    $telefono = get_option('mi_plugin_de_whatsapp_telefono');
    $retraso = get_option('mi_plugin_de_whatsapp_retraso', 0) * 1000; // Convertir segundos en milisegundos
    $posicion = get_option('mi_plugin_de_whatsapp_posicion', 'right');
    $mensaje = get_option('mi_plugin_de_whatsapp_mensaje', '');

    if ($telefono) {
        ?>
        <script type="text/javascript">
			var iconPosition = '<?php echo $posicion; ?>';
			var messagePosition = '<?php echo $posicion; ?>';
			
            function isMobileDevice() {
                return (typeof window.orientation !== "undefined") || (navigator.userAgent.indexOf('IEMobile') !== -1);
            };

            function openWhatsApp() {
                if (isMobileDevice()) {
                    window.open('whatsapp://send?phone=<?php echo $telefono; ?>', '_blank');
                } else {
                    window.open('https://web.whatsapp.com/send?phone=<?php echo $telefono; ?>', '_blank');
                }
            }
			
			function getIconPositionStyle(posicion) {
                var positionStyle = {};

                switch (posicion) {
                    case 'left':
                        positionStyle.left = '20px';
                        break;
                    case 'center':
                        positionStyle.left = '50%';
                        positionStyle.transform = 'translateX(-50%)';
                        break;
                    case 'right':
                    default:
                        positionStyle.right = '20px';
                        break;
                }

                return positionStyle;
            }
			
			function getMessagePositionStyle(posicion) {
                var positionStyle = {};

                switch (posicion) {
                    case 'left':
                        positionStyle.left = '20px';
                        break;
                    case 'center':
                        positionStyle.left = '50%';
                        positionStyle.transform = 'translateX(-50%)';
                        break;
                    case 'right':
                    default:
                        positionStyle.right = '20px';
                        break;
                }

                return positionStyle;
            }
			
			/*actualizacion*/
			
			
			function openWhatsAppForm() {
				
				if (jQuery('#whatsapp-form-container').length > 0) {
                					
					return; // Si el formulario ya está en la página, no hacer nada
                }
				
				
                var formHTML = [
                    //'<div id="whatsapp-form-container" style="position: fixed; bottom: 70px; z-index: 1001; display: none;">',
					 '<div id="whatsapp-form-container" style="position: fixed; bottom: 70px; z-index: 1001; display: none; max-width: 300px; width: 100%;">',
                        '<form id="whatsapp-form" style="padding: 5px; background-color: #f1f1f1; border-radius: 10px; box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);">',
                            '<div style="margin-bottom: 10px; text-align: center;">',
                                //'<label for="whatsapp-form-name">Nombre:</label>',
                                '<input type="text" id="whatsapp-form-name" name="name" style="width: 90%; padding: 5px; margin-top: 5px;" placeholder="Nombre" required>',
                            '</div>',
                            '<div style="margin-bottom: 10px; text-align: center;">',
                                //'<label for="whatsapp-form-message">Mensaje:</label>',
                                '<textarea id="whatsapp-form-message" name="message" style="width: 90%; padding: 5px; margin-top: 5px; resize: none;" rows="4" placeholder="Mensaje" required></textarea>',
                            '</div>',
							'<div style="margin-bottom: 10px; text-align: center;">',
								'<button type="submit" style="background-color: #22c15e; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">Enviar</button>',
								'<button type="button" id="close-form" style="background-color: #f1f1f1; color: #333; padding: 10px 20px; border: 1px solid #ccc; border-radius: 5px; cursor: pointer; margin-left: 10px;">Cerrar</button>',
							'</div>',
                        '</form>',
                    '</div>'
                ].join('');
				
				var formPositionStyle = getIconPositionStyle(iconPosition);
				var formTopPosition = parseInt(formPositionStyle.bottom) + 60; // Sumar 60 para ubicar el formulario encima del ícono


                var $formContainer = jQuery(formHTML);
                
				$formContainer.css({
					bottom: formTopPosition + 'px',
					left: formPositionStyle.left,
					right: formPositionStyle.right,
					transform: formPositionStyle.transform
				});
				
				jQuery('body').append($formContainer);
				
				//console.log('Form appended to body');

				
                $formContainer.slideDown();

                jQuery('#whatsapp-form').on('submit', function(e) {
                    e.preventDefault();

                    var name = jQuery('#whatsapp-form-name').val().trim();
                    var message = jQuery('#whatsapp-form-message').val().trim();
                    //var encodedMessage = encodeURIComponent(name + ': ' + message);
					
					var formattedMessage = "Mi nombre es " + name + ", " + message; // Agregar "Mi nombre es", el nombre, y un salto de línea (%0A)
					var encodedMessage = encodeURIComponent(formattedMessage);
					
                    if (isMobileDevice()) {
                        window.open('whatsapp://send?phone=<?php echo $telefono; ?>&text=' + encodedMessage, '_blank');
                    } else {
                        window.open('https://web.whatsapp.com/send?phone=<?php echo $telefono; ?>&text=' + encodedMessage, '_blank');
                    }

                    /*
					$formContainer.slideUp(function() {
                        $formContainer.remove();
                    });
					*/
                });
				
				//controlador de eventos click del boton Cerrar
				jQuery(document).on('click', '#whatsapp-form button[type="button"]', function() {
				  closeWhatsAppForm();
				});
            }
			
			
			function closeWhatsAppForm() {
			  //console.log('closeWhatsAppForm() called');
			  var $formContainer = jQuery('#whatsapp-form-container');
			  //console.log($formContainer);
			  $formContainer.slideUp(function() {
				//console.log('slide animation complete');
				$formContainer.remove();
			  });
			}
			
			
			/*--------------*/

            jQuery(document).ready(function($) {
				setTimeout(function() {
					var positionStyle = getIconPositionStyle('<?php echo $posicion; ?>');
					var messagePositionStyle = getMessagePositionStyle('<?php echo $posicion; ?>');
					
					


                    var whatsappIcon = jQuery('<a>', {
                        //onclick: 'openWhatsApp()',
						//onclick: 'openWhatsAppForm()', // Cambiar el onclick para abrir el formulario en lugar de WhatsApp directamente
						click: function() { openWhatsAppForm(this); }, // Cambiar el onclick para abrir el formulario en lugar de WhatsApp directamente
                        css: $.extend({
                            position: 'fixed',
                            bottom: '20px',
                            zIndex: '1000',
                            width: '50px',
                            height: '50px',
                            display: 'block',
                            cursor: 'pointer'
                        }, positionStyle),
                        html: '<img src="<?php echo plugins_url('whatsapp.png', __FILE__); ?>" alt="WhatsApp" style="width: 100%; height: 100%;" />'
                    });

                    var whatsappMessage = $('<div>', {
                        css: $.extend({
                            position: 'fixed',
                            bottom: '80px',
                            zIndex: '1000',
                            backgroundColor: '#22c15e',
                            borderRadius: '25px',
                            padding: '10px 20px',
                            color: 'white',
                            fontWeight: 'bold',
                            display: '<?php echo $mensaje ? "block" : "none"; ?>'
                        }, messagePositionStyle),
                        text: '<?php echo $mensaje; ?>'
                    });
					
					$('body').append(whatsappIcon);
                    setTimeout(function() {
                        $('body').append(whatsappMessage);
                    }, 3000); // 3 segundos de retraso para mostrar el mensaje
					
					
					
				}, <?php echo $retraso; ?>);
            });
        </script>
        <?php
    }
}



// Agregar la función como acción en WooCommerce
add_action('woocommerce_proceed_to_checkout', 'finalizar_compra_por_whatsapp', 20);


// Función para mostrar el botón "Finalizar compra por WhatsApp"
function finalizar_compra_por_whatsapp() {
	if (!get_option('mi_plugin_de_whatsapp_boton_carrito')) {
        return;
    }
    if (function_exists('is_cart') && is_cart() && WC()->cart->get_cart_contents_count() > 0) {
        $telefono = get_option('mi_plugin_de_whatsapp_telefono');

        if ($telefono) {
        // Obtener los detalles del carrito
        $carrito = WC()->cart->get_cart();
		
        ?>
		
		<style>
			#finalizar_compra_whatsapp {
				background-color: #25D366;
				border: none;
				border-radius: 30px;
				color: white;
				cursor: pointer;
				font-weight: bold;
				padding: 10px 20px;
				display: inline-flex;
				align-items: center;
			}

			#finalizar_compra_whatsapp img {
				height: 24px;
				margin-right: 8px;
			}
		</style>
	
	
        <script>
			
            function isMobileDevice() {
                return (typeof window.orientation !== "undefined") || (navigator.userAgent.indexOf('IEMobile') !== -1);
            };

            function redirectToWhatsApp() {
                // Construye el mensaje con saltos de línea
                var mensaje = "Me gustaría finalizar mi compra. Aquí están los detalles de mi carrito:%0A%0A";
				var total = 0;
                <?php foreach ($carrito as $item) { ?>
                    var nombre_producto = "<?php echo $item['data']->get_name(); ?>";
                    var cantidad = "<?php echo $item['quantity']; ?>";
                    var precio = "<?php echo $item['data']->get_price(); ?>";
                    var subtotal = "<?php echo $item['data']->get_price() * $item['quantity']; ?>";
                    
					total += parseFloat(subtotal);
					
					mensaje += "Producto: " + nombre_producto + "%0ACantidad: " + cantidad + "%0APrecio: " + precio + "%0ASubtotal: " + subtotal + "%0A%0A";
					
                <?php } ?>
								
				// Agregar el total del carrito al mensaje
				mensaje += "Total: " + total.toFixed(2) + "%0A%0A";

                // Construye la URL de WhatsApp y redirige al usuario
                var baseURL = isMobileDevice() ? 'https://api.whatsapp.com/send?phone=' : 'https://web.whatsapp.com/send?phone=';
                var whatsappUrl = baseURL + "<?php echo $telefono; ?>&text=" + mensaje;
                window.location.href = whatsappUrl;
            }
        </script>
        <button class="button alt" id="finalizar_compra_whatsapp" onclick="redirectToWhatsApp()">Finalizar compra por WhatsApp</button>

        <?php
		}
    }
}



