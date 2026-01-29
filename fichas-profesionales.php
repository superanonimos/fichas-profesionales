<?php
/**
 * Plugin Name: Fichas Profesionales
 * Description: Directorio de profesionales de las artes escénicas con membresías de pago mediante WooCommerce.
 * Version: 0.1.0
 * Author: Sergi
 * Text Domain: fichas-profesionales
 * Requires at least: 6.0
 * Requires PHP: 7.4
 */

defined( 'ABSPATH' ) || exit;

function fichas_profesionales_init() {
	register_post_type(
		'fichas_profesionales_profile',
		array(
			'labels'       => array(
				'name'               => __( 'Profesionales', 'fichas-profesionales' ),
				'singular_name'      => __( 'Profesional', 'fichas-profesionales' ),
				'add_new'            => __( 'Añadir nuevo', 'fichas-profesionales' ),
				'add_new_item'       => __( 'Añadir nuevo profesional', 'fichas-profesionales' ),
				'edit_item'          => __( 'Editar profesional', 'fichas-profesionales' ),
				'new_item'           => __( 'Nuevo profesional', 'fichas-profesionales' ),
				'all_items'          => __( 'Todos los profesionales', 'fichas-profesionales' ),
				'view_item'          => __( 'Ver profesional', 'fichas-profesionales' ),
				'search_items'       => __( 'Buscar profesionales', 'fichas-profesionales' ),
				'not_found'          => __( 'No se han encontrado profesionales', 'fichas-profesionales' ),
				'not_found_in_trash' => __( 'No se han encontrado profesionales en la papelera', 'fichas-profesionales' ),
			),
			'public'       => true,
			'has_archive'  => true,
			'show_in_rest' => true,
			'supports'     => array( 'title', 'editor', 'thumbnail' ),
			'rewrite'      => array(
				'slug' => 'profesionales',
			),
			'menu_icon'    => 'dashicons-id',
		)
	);
}

add_action( 'init', 'fichas_profesionales_init' );

function fichas_profesionales_register_shortcodes() {
	add_shortcode( 'fichas_profesionales_registro', 'fichas_profesionales_registration_form_shortcode' );
	add_shortcode( 'fichas_profesionales_directorio', 'fichas_profesionales_directory_shortcode' );
}

add_action( 'init', 'fichas_profesionales_register_shortcodes' );

function fichas_profesionales_registration_form_shortcode() {
	if ( is_user_logged_in() ) {
		$current_user = wp_get_current_user();
		ob_start();
		echo '<p>';
		echo esc_html( sprintf( __( 'Ya has iniciado sesión como %s. Puedes gestionar tu ficha desde tu área privada.', 'fichas-profesionales' ), $current_user->user_email ) );
		echo '</p>';
		return ob_get_clean();
	}

	$errors          = array();
	$success         = false;
	$membership_plan = '';
	$action          = isset( $_POST['fichas_profesionales_action'] ) ? sanitize_text_field( wp_unslash( $_POST['fichas_profesionales_action'] ) ) : '';

	if ( 'POST' === $_SERVER['REQUEST_METHOD'] && 'register' === $action ) {
		if ( ! isset( $_POST['fichas_profesionales_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['fichas_profesionales_nonce'] ) ), 'fichas_profesionales_register' ) ) {
			$errors[] = __( 'Por tu seguridad, vuelve a enviar el formulario.', 'fichas-profesionales' );
		} else {
			$first_name          = isset( $_POST['first_name'] ) ? sanitize_text_field( wp_unslash( $_POST['first_name'] ) ) : '';
			$last_name           = isset( $_POST['last_name'] ) ? sanitize_text_field( wp_unslash( $_POST['last_name'] ) ) : '';
			$user_email          = isset( $_POST['user_email'] ) ? sanitize_email( wp_unslash( $_POST['user_email'] ) ) : '';
			$phone               = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
			$profession          = isset( $_POST['profession'] ) ? sanitize_text_field( wp_unslash( $_POST['profession'] ) ) : '';
			$address_street      = isset( $_POST['address_street'] ) ? sanitize_text_field( wp_unslash( $_POST['address_street'] ) ) : '';
			$address_number      = isset( $_POST['address_number'] ) ? sanitize_text_field( wp_unslash( $_POST['address_number'] ) ) : '';
			$address_department  = isset( $_POST['address_department'] ) ? sanitize_text_field( wp_unslash( $_POST['address_department'] ) ) : '';
			$city                = isset( $_POST['city'] ) ? sanitize_text_field( wp_unslash( $_POST['city'] ) ) : '';
			$region              = isset( $_POST['region'] ) ? sanitize_text_field( wp_unslash( $_POST['region'] ) ) : '';
			$website             = isset( $_POST['website'] ) ? sanitize_text_field( wp_unslash( $_POST['website'] ) ) : '';
			$instagram           = isset( $_POST['instagram'] ) ? sanitize_text_field( wp_unslash( $_POST['instagram'] ) ) : '';
			$facebook            = isset( $_POST['facebook'] ) ? sanitize_text_field( wp_unslash( $_POST['facebook'] ) ) : '';
			$youtube             = isset( $_POST['youtube'] ) ? sanitize_text_field( wp_unslash( $_POST['youtube'] ) ) : '';
			$language            = isset( $_POST['language'] ) ? sanitize_text_field( wp_unslash( $_POST['language'] ) ) : '';
			$search_keywords     = isset( $_POST['search_keywords'] ) ? sanitize_text_field( wp_unslash( $_POST['search_keywords'] ) ) : '';
			$linkedin            = isset( $_POST['linkedin'] ) ? sanitize_text_field( wp_unslash( $_POST['linkedin'] ) ) : '';
			$tiktok              = isset( $_POST['tiktok'] ) ? sanitize_text_field( wp_unslash( $_POST['tiktok'] ) ) : '';
			$demo_video          = isset( $_POST['demo_video'] ) ? esc_url_raw( wp_unslash( $_POST['demo_video'] ) ) : '';
			$membership_plan_raw = isset( $_POST['membership_plan'] ) ? sanitize_text_field( wp_unslash( $_POST['membership_plan'] ) ) : '';
			$address_1           = trim( $address_street . ' ' . $address_number );
			$address_2           = $address_department;
			$allowed_plans       = array( 'quarterly', 'semiannual', 'annual' );

			if ( in_array( $membership_plan_raw, $allowed_plans, true ) ) {
				$membership_plan = $membership_plan_raw;
			}

			if ( '' === $first_name ) {
				$errors[] = __( 'Indica tu nombre.', 'fichas-profesionales' );
			}

			if ( '' === $last_name ) {
				$errors[] = __( 'Indica tus apellidos.', 'fichas-profesionales' );
			}

			if ( '' === $user_email || ! is_email( $user_email ) ) {
				$errors[] = __( 'Revisa tu correo electrónico, parece no ser válido.', 'fichas-profesionales' );
			} elseif ( email_exists( $user_email ) ) {
				$errors[] = __( 'Ya existe una cuenta con este correo. Prueba a iniciar sesión.', 'fichas-profesionales' );
			}

			if ( '' === $phone ) {
				$errors[] = __( 'Cuéntanos un teléfono de contacto.', 'fichas-profesionales' );
			}

			if ( '' === $profession ) {
				$errors[] = __( 'Indica tu especialidad o profesión.', 'fichas-profesionales' );
			}

			if ( '' === $address_street || '' === $address_number ) {
				$errors[] = __( 'Necesitamos tu calle y número para completar la dirección.', 'fichas-profesionales' );
			}

			if ( '' === $city ) {
				$errors[] = __( 'Indica la ciudad donde trabajas o resides.', 'fichas-profesionales' );
			}

			if ( '' === $region ) {
				$errors[] = __( 'Indica tu región o provincia.', 'fichas-profesionales' );
			}

			if ( '' === $membership_plan ) {
				$errors[] = __( 'Elige el plan de membresía que mejor se adapte a ti.', 'fichas-profesionales' );
			}

			$max_file_size       = 3 * 1024 * 1024;
			$allowed_image_mimes = array(
				'jpg|jpeg|jpe' => 'image/jpeg',
				'png'          => 'image/png',
				'gif'          => 'image/gif',
				'webp'         => 'image/webp',
			);
			$allowed_cv_mimes    = array(
				'pdf'          => 'application/pdf',
				'doc'          => 'application/msword',
				'docx'         => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
				'jpg|jpeg|jpe' => 'image/jpeg',
				'png'          => 'image/png',
			);

			$main_image_url   = fichas_profesionales_upload_file( 'main_image', $allowed_image_mimes, $max_file_size, $errors );
			$gallery_urls     = array();
			$gallery_fields   = array( 'gallery_image_1', 'gallery_image_2', 'gallery_image_3', 'gallery_image_4' );
			$gallery_urls_raw = array();

			foreach ( $gallery_fields as $gallery_field ) {
				$url = fichas_profesionales_upload_file( $gallery_field, $allowed_image_mimes, $max_file_size, $errors );
				if ( '' !== $url ) {
					$gallery_urls_raw[] = $url;
				}
			}

			if ( ! empty( $gallery_urls_raw ) ) {
				$gallery_urls = array_slice( $gallery_urls_raw, 0, 4 );
			}

			$cv_url = fichas_profesionales_upload_file( 'cv_file', $allowed_cv_mimes, $max_file_size, $errors );

			if ( empty( $errors ) ) {
				$password  = wp_generate_password( 12, true );
				$user_data = array(
					'user_login' => $user_email,
					'user_email' => $user_email,
					'user_pass'  => $password,
					'first_name' => $first_name,
					'last_name'  => $last_name,
					'role'       => 'subscriber',
				);

				$user_id = wp_insert_user( $user_data );

				if ( is_wp_error( $user_id ) ) {
					$errors[] = __( 'No hemos podido crear tu cuenta en este momento. Inténtalo de nuevo en unos minutos.', 'fichas-profesionales' );
				} else {
					update_user_meta( $user_id, 'first_name', $first_name );
					update_user_meta( $user_id, 'last_name', $last_name );
					update_user_meta( $user_id, 'profession', $profession );
					update_user_meta( $user_id, 'phone', $phone );
					update_user_meta( $user_id, 'address_street', $address_street );
					update_user_meta( $user_id, 'address_number', $address_number );
					update_user_meta( $user_id, 'address_department', $address_department );
					update_user_meta( $user_id, 'city', $city );
					update_user_meta( $user_id, 'region', $region );
					update_user_meta( $user_id, 'website', $website );
					update_user_meta( $user_id, 'instagram', $instagram );
					update_user_meta( $user_id, 'facebook', $facebook );
					update_user_meta( $user_id, 'youtube', $youtube );
					update_user_meta( $user_id, 'language', $language );
					update_user_meta( $user_id, 'search_keywords', $search_keywords );
					update_user_meta( $user_id, 'linkedin', $linkedin );
					update_user_meta( $user_id, 'tiktok', $tiktok );
					update_user_meta( $user_id, 'demo_video', $demo_video );
					update_user_meta( $user_id, 'billing_first_name', $first_name );
					update_user_meta( $user_id, 'billing_last_name', $last_name );
					update_user_meta( $user_id, 'billing_phone', $phone );
					update_user_meta( $user_id, 'billing_email', $user_email );
					update_user_meta( $user_id, 'billing_address_1', $address_1 );
					update_user_meta( $user_id, 'billing_address_2', $address_2 );
					update_user_meta( $user_id, 'billing_city', $city );
					update_user_meta( $user_id, 'billing_state', $region );
					update_user_meta( $user_id, 'fp_membership_plan', $membership_plan );
					update_user_meta( $user_id, 'fp_password_set', 0 );

					if ( '' !== $main_image_url ) {
						update_user_meta( $user_id, 'fp_main_image_url', esc_url_raw( $main_image_url ) );
					}

					if ( ! empty( $gallery_urls ) ) {
						$sanitized_gallery = array();
						foreach ( $gallery_urls as $url ) {
							$sanitized_gallery[] = esc_url_raw( $url );
						}
						update_user_meta( $user_id, 'fp_gallery_image_urls', $sanitized_gallery );
					}

					if ( '' !== $cv_url ) {
						update_user_meta( $user_id, 'fp_cv_url', esc_url_raw( $cv_url ) );
					}

					$success = true;

					wp_set_current_user( $user_id );
					wp_set_auth_cookie( $user_id );

					if ( $membership_plan && function_exists( 'wc_get_checkout_url' ) && function_exists( 'WC' ) ) {
						$product_id = (int) apply_filters( 'fichas_profesionales_membership_product_id', 0, $membership_plan, $user_id );
						if ( $product_id > 0 && WC()->cart ) {
							WC()->cart->add_to_cart( $product_id );
							$checkout_url = wc_get_checkout_url();
							wp_safe_redirect( $checkout_url );
							exit;
						}
					}
				}
			}
		}
	}

	ob_start();

	if ( ! empty( $errors ) ) {
		echo '<div class="fp-errors">';
		foreach ( $errors as $error ) {
			echo '<p>' . esc_html( $error ) . '</p>';
		}
		echo '</div>';
	}

	if ( $success ) {
		echo '<div class="fp-success">';
		echo esc_html__( '¡Bienvenido! Hemos creado tu cuenta y estamos preparando tu pago de membresía.', 'fichas-profesionales' );
		echo '</div>';
		return ob_get_clean();
	}

	$first_name_value         = isset( $_POST['first_name'] ) ? wp_unslash( $_POST['first_name'] ) : '';
	$last_name_value          = isset( $_POST['last_name'] ) ? wp_unslash( $_POST['last_name'] ) : '';
	$user_email_value         = isset( $_POST['user_email'] ) ? wp_unslash( $_POST['user_email'] ) : '';
	$phone_value              = isset( $_POST['phone'] ) ? wp_unslash( $_POST['phone'] ) : '';
	$profession_value         = isset( $_POST['profession'] ) ? wp_unslash( $_POST['profession'] ) : '';
	$address_street_value     = isset( $_POST['address_street'] ) ? wp_unslash( $_POST['address_street'] ) : '';
	$address_number_value     = isset( $_POST['address_number'] ) ? wp_unslash( $_POST['address_number'] ) : '';
	$address_department_value = isset( $_POST['address_department'] ) ? wp_unslash( $_POST['address_department'] ) : '';
	$city_value               = isset( $_POST['city'] ) ? wp_unslash( $_POST['city'] ) : '';
	$region_value             = isset( $_POST['region'] ) ? wp_unslash( $_POST['region'] ) : '';
	$website_value            = isset( $_POST['website'] ) ? wp_unslash( $_POST['website'] ) : '';
	$instagram_value          = isset( $_POST['instagram'] ) ? wp_unslash( $_POST['instagram'] ) : '';
	$facebook_value           = isset( $_POST['facebook'] ) ? wp_unslash( $_POST['facebook'] ) : '';
	$youtube_value            = isset( $_POST['youtube'] ) ? wp_unslash( $_POST['youtube'] ) : '';
	$language_value           = isset( $_POST['language'] ) ? wp_unslash( $_POST['language'] ) : '';
	$search_keywords_value    = isset( $_POST['search_keywords'] ) ? wp_unslash( $_POST['search_keywords'] ) : '';
	$linkedin_value           = isset( $_POST['linkedin'] ) ? wp_unslash( $_POST['linkedin'] ) : '';
	$tiktok_value             = isset( $_POST['tiktok'] ) ? wp_unslash( $_POST['tiktok'] ) : '';
	$demo_video_value         = isset( $_POST['demo_video'] ) ? wp_unslash( $_POST['demo_video'] ) : '';
	$membership_plan_value    = isset( $_POST['membership_plan'] ) ? sanitize_text_field( wp_unslash( $_POST['membership_plan'] ) ) : '';

	echo '<form method="post" enctype="multipart/form-data" class="fp-registration-form">';

	wp_nonce_field( 'fichas_profesionales_register', 'fichas_profesionales_nonce' );

	echo '<input type="hidden" name="fichas_profesionales_action" value="register" />';

	echo '<p>';
	echo '<label for="fp_first_name">' . esc_html__( 'Nombre', 'fichas-profesionales' ) . '</label>';
	echo '<input type="text" id="fp_first_name" name="first_name" value="' . esc_attr( $first_name_value ) . '" required />';
	echo '</p>';

	echo '<p>';
	echo '<label for="fp_last_name">' . esc_html__( 'Apellidos', 'fichas-profesionales' ) . '</label>';
	echo '<input type="text" id="fp_last_name" name="last_name" value="' . esc_attr( $last_name_value ) . '" required />';
	echo '</p>';

	echo '<p>';
	echo '<label for="fp_user_email">' . esc_html__( 'Correo electrónico profesional', 'fichas-profesionales' ) . '</label>';
	echo '<input type="email" id="fp_user_email" name="user_email" value="' . esc_attr( $user_email_value ) . '" required />';
	echo '</p>';

	echo '<p>';
	echo '<label for="fp_phone">' . esc_html__( 'Teléfono de contacto', 'fichas-profesionales' ) . '</label>';
	echo '<input type="text" id="fp_phone" name="phone" value="' . esc_attr( $phone_value ) . '" required />';
	echo '</p>';

	echo '<p>';
	echo '<label for="fp_profession">' . esc_html__( 'Profesión o especialidad', 'fichas-profesionales' ) . '</label>';
	echo '<input type="text" id="fp_profession" name="profession" value="' . esc_attr( $profession_value ) . '" required />';
	echo '</p>';

	echo '<p>';
	echo '<label for="fp_address_street">' . esc_html__( 'Calle', 'fichas-profesionales' ) . '</label>';
	echo '<input type="text" id="fp_address_street" name="address_street" value="' . esc_attr( $address_street_value ) . '" required />';
	echo '</p>';

	echo '<p>';
	echo '<label for="fp_address_number">' . esc_html__( 'Número', 'fichas-profesionales' ) . '</label>';
	echo '<input type="text" id="fp_address_number" name="address_number" value="' . esc_attr( $address_number_value ) . '" required />';
	echo '</p>';

	echo '<p>';
	echo '<label for="fp_address_department">' . esc_html__( 'Departamento / piso', 'fichas-profesionales' ) . '</label>';
	echo '<input type="text" id="fp_address_department" name="address_department" value="' . esc_attr( $address_department_value ) . '" />';
	echo '</p>';

	echo '<p>';
	echo '<label for="fp_city">' . esc_html__( 'Ciudad', 'fichas-profesionales' ) . '</label>';
	echo '<input type="text" id="fp_city" name="city" value="' . esc_attr( $city_value ) . '" required />';
	echo '</p>';

	echo '<p>';
	echo '<label for="fp_region">' . esc_html__( 'Región / provincia', 'fichas-profesionales' ) . '</label>';
	echo '<input type="text" id="fp_region" name="region" value="' . esc_attr( $region_value ) . '" required />';
	echo '</p>';

	echo '<p>';
	echo '<label for="fp_language">' . esc_html__( 'Idiomas de trabajo', 'fichas-profesionales' ) . '</label>';
	echo '<input type="text" id="fp_language" name="language" value="' . esc_attr( $language_value ) . '" placeholder="' . esc_attr__( 'Ej.: Español, Inglés, Catalán', 'fichas-profesionales' ) . '" />';
	echo '</p>';

	echo '<p>';
	echo '<label for="fp_website">' . esc_html__( 'Sitio web o portafolio', 'fichas-profesionales' ) . '</label>';
	echo '<input type="url" id="fp_website" name="website" value="' . esc_attr( $website_value ) . '" />';
	echo '</p>';

	echo '<p>';
	echo '<label for="fp_instagram">' . esc_html__( 'Instagram (usuario o enlace)', 'fichas-profesionales' ) . '</label>';
	echo '<input type="text" id="fp_instagram" name="instagram" value="' . esc_attr( $instagram_value ) . '" />';
	echo '</p>';

	echo '<p>';
	echo '<label for="fp_facebook">' . esc_html__( 'Facebook (página o perfil)', 'fichas-profesionales' ) . '</label>';
	echo '<input type="text" id="fp_facebook" name="facebook" value="' . esc_attr( $facebook_value ) . '" />';
	echo '</p>';

	echo '<p>';
	echo '<label for="fp_youtube">' . esc_html__( 'YouTube (canal o enlace)', 'fichas-profesionales' ) . '</label>';
	echo '<input type="text" id="fp_youtube" name="youtube" value="' . esc_attr( $youtube_value ) . '" />';
	echo '</p>';

	echo '<p>';
	echo '<label for="fp_linkedin">' . esc_html__( 'LinkedIn (perfil profesional)', 'fichas-profesionales' ) . '</label>';
	echo '<input type="text" id="fp_linkedin" name="linkedin" value="' . esc_attr( $linkedin_value ) . '" />';
	echo '</p>';

	echo '<p>';
	echo '<label for="fp_tiktok">' . esc_html__( 'TikTok (usuario o enlace)', 'fichas-profesionales' ) . '</label>';
	echo '<input type="text" id="fp_tiktok" name="tiktok" value="' . esc_attr( $tiktok_value ) . '" />';
	echo '</p>';

	echo '<p>';
	echo '<label for="fp_demo_video">' . esc_html__( 'Vídeo demo principal (YouTube, Vimeo, etc.)', 'fichas-profesionales' ) . '</label>';
	echo '<input type="url" id="fp_demo_video" name="demo_video" value="' . esc_attr( $demo_video_value ) . '" placeholder="' . esc_attr__( 'Pega aquí la URL del vídeo', 'fichas-profesionales' ) . '" />';
	echo '</p>';

	echo '<p>';
	echo '<label for="fp_search_keywords">' . esc_html__( 'Palabras clave para el buscador', 'fichas-profesionales' ) . '</label>';
	echo '<input type="text" id="fp_search_keywords" name="search_keywords" value="' . esc_attr( $search_keywords_value ) . '" placeholder="' . esc_attr__( 'Ej.: teatro físico, clown, danza contemporánea', 'fichas-profesionales' ) . '" />';
	echo '</p>';

	echo '<p>';
	echo esc_html__( 'Elige tu plan de membresía', 'fichas-profesionales' );
	echo '<br />';
	echo '<label>';
	echo '<input type="radio" name="membership_plan" value="quarterly"' . checked( 'quarterly', $membership_plan_value, false ) . ' />';
	echo ' ' . esc_html__( 'Plan Trimestral · 3 meses de visibilidad', 'fichas-profesionales' );
	echo '</label><br />';
	echo '<label>';
	echo '<input type="radio" name="membership_plan" value="semiannual"' . checked( 'semiannual', $membership_plan_value, false ) . ' />';
	echo ' ' . esc_html__( 'Plan Semestral · 6 meses de visibilidad', 'fichas-profesionales' );
	echo '</label><br />';
	echo '<label>';
	echo '<input type="radio" name="membership_plan" value="annual"' . checked( 'annual', $membership_plan_value, false ) . ' />';
	echo ' ' . esc_html__( 'Plan Anual · 12 meses de visibilidad', 'fichas-profesionales' );
	echo '</label>';
	echo '</p>';

	echo '<p>';
	echo '<label for="fp_main_image">' . esc_html__( 'Retrato principal (máx. 3 MB, JPG/PNG/GIF/WEBP)', 'fichas-profesionales' ) . '</label>';
	echo '<input type="file" id="fp_main_image" name="main_image" accept=".jpg,.jpeg,.png,.gif,.webp,image/jpeg,image/png,image/gif,image/webp" />';
	echo '</p>';

	echo '<p>';
	echo '<label>' . esc_html__( 'Imágenes de escena o portafolio (hasta 4, máx. 3 MB c/u)', 'fichas-profesionales' ) . '</label><br />';
	echo '<input type="file" name="gallery_image_1" accept=".jpg,.jpeg,.png,.gif,.webp,image/jpeg,image/png,image/gif,image/webp" /><br />';
	echo '<input type="file" name="gallery_image_2" accept=".jpg,.jpeg,.png,.gif,.webp,image/jpeg,image/png,image/gif,image/webp" /><br />';
	echo '<input type="file" name="gallery_image_3" accept=".jpg,.jpeg,.png,.gif,.webp,image/jpeg,image/png,image/gif,image/webp" /><br />';
	echo '<input type="file" name="gallery_image_4" accept=".jpg,.jpeg,.png,.gif,.webp,image/jpeg,image/png,image/gif,image/webp" />';
	echo '</p>';

	echo '<p>';
	echo '<label for="fp_cv_file">' . esc_html__( 'Currículum artístico (PDF, Word, JPG, PNG, máx. 3 MB)', 'fichas-profesionales' ) . '</label>';
	echo '<input type="file" id="fp_cv_file" name="cv_file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,image/jpeg,image/png" />';
	echo '</p>';

	echo '<p>';
	echo '<button type="submit">' . esc_html__( 'Crear mi perfil profesional', 'fichas-profesionales' ) . '</button>';
	echo '</p>';

	echo '</form>';

	return ob_get_clean();
}

function fichas_profesionales_directory_shortcode() {
	$profession_filter = isset( $_GET['fp_profession'] ) ? sanitize_text_field( wp_unslash( $_GET['fp_profession'] ) ) : '';
	$city_filter       = isset( $_GET['fp_city'] ) ? sanitize_text_field( wp_unslash( $_GET['fp_city'] ) ) : '';
	$region_filter     = isset( $_GET['fp_region'] ) ? sanitize_text_field( wp_unslash( $_GET['fp_region'] ) ) : '';
	$language_filter   = isset( $_GET['fp_language'] ) ? sanitize_text_field( wp_unslash( $_GET['fp_language'] ) ) : '';
	$keywords_filter   = isset( $_GET['fp_keywords'] ) ? sanitize_text_field( wp_unslash( $_GET['fp_keywords'] ) ) : '';
	$current_page      = isset( $_GET['fp_page'] ) ? max( 1, (int) $_GET['fp_page'] ) : 1;
	$per_page          = 12;
	$offset            = ( $current_page - 1 ) * $per_page;
	$timestamp         = current_time( 'timestamp' );
	$today             = gmdate( 'Y-m-d', $timestamp );

	$meta_query = array(
		'relation' => 'AND',
		array(
			'key'     => 'fp_membership_expires',
			'value'   => $today,
			'compare' => '>=',
			'type'    => 'DATE',
		),
		array(
			'key'     => 'fp_membership_status',
			'value'   => 'expired',
			'compare' => '!=',
		),
	);

	if ( '' !== $profession_filter ) {
		$meta_query[] = array(
			'key'     => 'profession',
			'value'   => $profession_filter,
			'compare' => 'LIKE',
		);
	}

	if ( '' !== $city_filter ) {
		$meta_query[] = array(
			'key'     => 'city',
			'value'   => $city_filter,
			'compare' => 'LIKE',
		);
	}

	if ( '' !== $region_filter ) {
		$meta_query[] = array(
			'key'     => 'region',
			'value'   => $region_filter,
			'compare' => 'LIKE',
		);
	}

	if ( '' !== $language_filter ) {
		$meta_query[] = array(
			'key'     => 'language',
			'value'   => $language_filter,
			'compare' => 'LIKE',
		);
	}

	if ( '' !== $keywords_filter ) {
		$meta_query[] = array(
			'key'     => 'search_keywords',
			'value'   => $keywords_filter,
			'compare' => 'LIKE',
		);
	}

	$args = array(
		'number'     => $per_page,
		'offset'     => $offset,
		'meta_query' => $meta_query,
	);

	$user_query = new WP_User_Query( $args );
	$users      = $user_query->get_results();
	$total      = (int) $user_query->get_total();
	$total_pages = $per_page > 0 ? (int) ceil( $total / $per_page ) : 1;

	ob_start();

	echo '<style>';
	echo '.fp-directory-wrapper{max-width:1200px;margin:0 auto;}';
	echo '.fp-directory-filters{margin-bottom:1.5rem;padding:1rem 1.5rem;border-radius:12px;background:#f8fafc;border:1px solid #e2e8f0;display:flex;flex-wrap:wrap;gap:0.75rem;}';
	echo '.fp-directory-filters .fp-field{flex:1 1 150px;min-width:140px;}';
	echo '.fp-directory-filters label{display:block;font-size:0.78rem;letter-spacing:.08em;text-transform:uppercase;color:#64748b;margin-bottom:0.25rem;}';
	echo '.fp-directory-filters input{width:100%;padding:0.45rem 0.55rem;border-radius:999px;border:1px solid #cbd5f5;background:#ffffff;font-size:0.9rem;}';
	echo '.fp-directory-filters-actions{display:flex;align-items:flex-end;gap:0.5rem;}';
	echo '.fp-directory-filters button,.fp-directory-filters a.fp-clear{border-radius:999px;padding:0.5rem 1rem;font-size:0.85rem;border:none;cursor:pointer;}';
	echo '.fp-directory-filters button{background:#0f766e;color:#ffffff;}';
	echo '.fp-directory-filters button:hover{background:#115e59;}';
	echo '.fp-directory-filters a.fp-clear{background:transparent;color:#64748b;text-decoration:none;border:1px solid #cbd5f5;}';
	echo '.fp-directory-filters a.fp-clear:hover{color:#0f172a;border-color:#94a3b8;}';
	echo '.fp-directory-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:1.5rem;}';
	echo '.fp-directory-card{background:#ffffff;border-radius:16px;box-shadow:0 10px 30px rgba(15,23,42,0.06);overflow:hidden;display:flex;flex-direction:column;}';
	echo '.fp-directory-card-image{position:relative;overflow:hidden;}';
	echo '.fp-directory-card-image img{width:100%;height:220px;object-fit:cover;display:block;}';
	echo '.fp-directory-card-body{padding:1rem 1.1rem 1.1rem;}';
	echo '.fp-directory-card-title{font-size:1.05rem;font-weight:600;margin:0 0 0.25rem;color:#0f172a;}';
	echo '.fp-directory-card-sub{font-size:0.9rem;color:#475569;margin:0 0 0.35rem;}';
	echo '.fp-directory-card-meta{font-size:0.85rem;color:#64748b;margin:0 0 0.35rem;}';
	echo '.fp-directory-card-meta strong{color:#475569;}';
	echo '.fp-directory-keywords{font-size:0.8rem;color:#94a3b8;margin:0 0 0.5rem;}';
	echo '.fp-directory-card-footer{padding:0 1.1rem 1.1rem;margin-top:auto;}';
	echo '.fp-directory-card-footer a{display:inline-block;border-radius:999px;padding:0.45rem 0.9rem;font-size:0.85rem;background:#0f766e;color:#ffffff;text-decoration:none;}';
	echo '.fp-directory-card-footer a:hover{background:#115e59;}';
	echo '.fp-directory-empty{padding:1rem 0;color:#64748b;font-size:0.95rem;}';
	echo '.fp-directory-pagination{margin-top:1.5rem;display:flex;justify-content:center;gap:0.35rem;flex-wrap:wrap;}';
	echo '.fp-directory-pagination a,.fp-directory-pagination span{min-width:2rem;height:2rem;display:inline-flex;align-items:center;justify-content:center;border-radius:999px;font-size:0.85rem;text-decoration:none;border:1px solid #e2e8f0;color:#475569;background:#ffffff;}';
	echo '.fp-directory-pagination .fp-page-current{background:#0f766e;border-color:#0f766e;color:#ffffff;}';
	echo '@media (max-width:960px){.fp-directory-grid{grid-template-columns:repeat(2,minmax(0,1fr));}}';
	echo '@media (max-width:640px){.fp-directory-grid{grid-template-columns:repeat(1,minmax(0,1fr));}.fp-directory-filters{flex-direction:column;}.fp-directory-filters .fp-field{min-width:100%;}.fp-directory-filters-actions{justify-content:flex-start;}}';
	echo '</style>';

	$base_url  = '';
	$current   = get_queried_object();
	if ( $current && isset( $current->ID ) ) {
		$base_url = get_permalink( $current->ID );
	}

	$form_action = $base_url ? $base_url : '';

	echo '<div class="fp-directory-wrapper">';

	echo '<form method="get" action="' . esc_url( $form_action ) . '" class="fp-directory-filters">';

	echo '<div class="fp-field">';
	echo '<label for="fp_profession">' . esc_html__( 'Profesión', 'fichas-profesionales' ) . '</label>';
	echo '<input type="text" id="fp_profession" name="fp_profession" value="' . esc_attr( $profession_filter ) . '" placeholder="' . esc_attr__( 'Ej.: Actor, bailarina…', 'fichas-profesionales' ) . '" />';
	echo '</div>';

	echo '<div class="fp-field">';
	echo '<label for="fp_city">' . esc_html__( 'Ciudad', 'fichas-profesionales' ) . '</label>';
	echo '<input type="text" id="fp_city" name="fp_city" value="' . esc_attr( $city_filter ) . '" placeholder="' . esc_attr__( 'Ej.: Barcelona', 'fichas-profesionales' ) . '" />';
	echo '</div>';

	echo '<div class="fp-field">';
	echo '<label for="fp_region">' . esc_html__( 'Región / provincia', 'fichas-profesionales' ) . '</label>';
	echo '<input type="text" id="fp_region" name="fp_region" value="' . esc_attr( $region_filter ) . '" placeholder="' . esc_attr__( 'Ej.: Catalunya', 'fichas-profesionales' ) . '" />';
	echo '</div>';

	echo '<div class="fp-field">';
	echo '<label for="fp_language">' . esc_html__( 'Idioma', 'fichas-profesionales' ) . '</label>';
	echo '<input type="text" id="fp_language" name="fp_language" value="' . esc_attr( $language_filter ) . '" placeholder="' . esc_attr__( 'Ej.: Español', 'fichas-profesionales' ) . '" />';
	echo '</div>';

	echo '<div class="fp-field">';
	echo '<label for="fp_keywords">' . esc_html__( 'Palabras clave', 'fichas-profesionales' ) . '</label>';
	echo '<input type="text" id="fp_keywords" name="fp_keywords" value="' . esc_attr( $keywords_filter ) . '" placeholder="' . esc_attr__( 'Ej.: clown, teatro físico…', 'fichas-profesionales' ) . '" />';
	echo '</div>';

	echo '<div class="fp-directory-filters-actions">';
	echo '<button type="submit">' . esc_html__( 'Buscar', 'fichas-profesionales' ) . '</button>';

	$clear_url = $base_url ? $base_url : '';

	if ( $clear_url ) {
		echo '<a class="fp-clear" href="' . esc_url( $clear_url ) . '">' . esc_html__( 'Limpiar filtros', 'fichas-profesionales' ) . '</a>';
	}

	echo '</div>';

	echo '</form>';

	if ( empty( $users ) ) {
		echo '<div class="fp-directory-empty">';
		echo esc_html__( 'De momento no hay profesionales que coincidan con tu búsqueda.', 'fichas-profesionales' );
		echo '</div>';
		echo '</div>';
		return ob_get_clean();
	}

	echo '<div class="fp-directory-grid">';

	foreach ( $users as $user ) {
		$user_id     = $user->ID;
		$profile_id  = (int) get_user_meta( $user_id, 'fp_profile_id', true );
		$profile_url = '';

		if ( $profile_id && 'fichas_profesionales_profile' === get_post_type( $profile_id ) && 'publish' === get_post_status( $profile_id ) ) {
			$profile_url = get_permalink( $profile_id );
		}

		if ( ! $profile_url ) {
			continue;
		}

		$first_name = get_user_meta( $user_id, 'first_name', true );
		$last_name  = get_user_meta( $user_id, 'last_name', true );
		$profession = get_user_meta( $user_id, 'profession', true );
		$city       = get_user_meta( $user_id, 'city', true );
		$region     = get_user_meta( $user_id, 'region', true );
		$language   = get_user_meta( $user_id, 'language', true );
		$keywords   = get_user_meta( $user_id, 'search_keywords', true );

		$full_name = trim( $first_name . ' ' . $last_name );

		if ( '' === $full_name ) {
			$full_name = $user->display_name ? $user->display_name : $user->user_login;
		}

		$main_image_url = get_user_meta( $user_id, 'fp_main_image_url', true );

		echo '<div class="fp-directory-card">';

		echo '<div class="fp-directory-card-image">';

		if ( $main_image_url ) {
			echo '<img src="' . esc_url( $main_image_url ) . '" alt="' . esc_attr( $full_name ) . '" />';
		} else {
			echo '<img src="' . esc_url( includes_url( 'images/media/default.png' ) ) . '" alt="' . esc_attr( $full_name ) . '" />';
		}

		echo '</div>';

		echo '<div class="fp-directory-card-body">';

		echo '<h3 class="fp-directory-card-title">' . esc_html( $full_name ) . '</h3>';

		if ( $profession ) {
			echo '<p class="fp-directory-card-sub">' . esc_html( $profession ) . '</p>';
		}

		if ( $city || $region ) {
			$location_parts = array();
			if ( $city ) {
				$location_parts[] = $city;
			}
			if ( $region ) {
				$location_parts[] = $region;
			}
			echo '<p class="fp-directory-card-meta"><strong>' . esc_html__( 'Ubicación', 'fichas-profesionales' ) . ':</strong> ' . esc_html( implode( ', ', $location_parts ) ) . '</p>';
		}

		if ( $language ) {
			echo '<p class="fp-directory-card-meta"><strong>' . esc_html__( 'Idiomas', 'fichas-profesionales' ) . ':</strong> ' . esc_html( $language ) . '</p>';
		}

		if ( $keywords ) {
			echo '<p class="fp-directory-keywords">' . esc_html( $keywords ) . '</p>';
		}

		echo '</div>';

		echo '<div class="fp-directory-card-footer">';
		echo '<a href="' . esc_url( $profile_url ) . '">' . esc_html__( 'Ver ficha completa', 'fichas-profesionales' ) . '</a>';
		echo '</div>';

		echo '</div>';
	}

	echo '</div>';

	if ( $total_pages > 1 ) {
		echo '<div class="fp-directory-pagination">';

		$base_args = array();

		if ( '' !== $profession_filter ) {
			$base_args['fp_profession'] = $profession_filter;
		}
		if ( '' !== $city_filter ) {
			$base_args['fp_city'] = $city_filter;
		}
		if ( '' !== $region_filter ) {
			$base_args['fp_region'] = $region_filter;
		}
		if ( '' !== $language_filter ) {
			$base_args['fp_language'] = $language_filter;
		}
		if ( '' !== $keywords_filter ) {
			$base_args['fp_keywords'] = $keywords_filter;
		}

		for ( $i = 1; $i <= $total_pages; $i++ ) {
			$page_args          = $base_args;
			$page_args['fp_page'] = $i;
			$page_url           = add_query_arg( $page_args, $base_url ? $base_url : '' );

			if ( $i === $current_page ) {
				echo '<span class="fp-page-current">' . esc_html( (string) $i ) . '</span>';
			} else {
				echo '<a href="' . esc_url( $page_url ) . '">' . esc_html( (string) $i ) . '</a>';
			}
		}

		echo '</div>';
	}

	echo '</div>';

	return ob_get_clean();
}

function fichas_profesionales_default_membership_products( $product_id, $plan, $user_id ) {
	if ( $product_id ) {
		return $product_id;
	}

	if ( ! function_exists( 'get_page_by_title' ) ) {
		return $product_id;
	}

	$plan_titles = array(
		'quarterly' => 'Trimestral',
		'semiannual' => 'Semestral',
		'annual' => 'Anual',
	);

	if ( ! isset( $plan_titles[ $plan ] ) ) {
		return $product_id;
	}

	$product = get_page_by_title( $plan_titles[ $plan ], OBJECT, 'product' );

	if ( $product && isset( $product->ID ) ) {
		return (int) $product->ID;
	}

	return $product_id;
}

add_filter( 'fichas_profesionales_membership_product_id', 'fichas_profesionales_default_membership_products', 10, 3 );

function fichas_profesionales_upload_file( $field_name, $allowed_mimes, $max_size, &$errors ) {
	if ( empty( $_FILES[ $field_name ]['name'] ) ) {
		return '';
	}

	$file = $_FILES[ $field_name ];

	if ( $file['size'] > $max_size ) {
		$errors[] = __( 'Uno de los archivos supera el tamaño máximo de 3 MB.', 'fichas-profesionales' );
		return '';
	}

	if ( ! function_exists( 'wp_check_filetype' ) ) {
		return '';
	}

	$filetype = wp_check_filetype( $file['name'], $allowed_mimes );

	if ( empty( $filetype['ext'] ) ) {
		$errors[] = __( 'Alguno de los archivos tiene un formato no permitido.', 'fichas-profesionales' );
		return '';
	}

	if ( ! function_exists( 'wp_handle_upload' ) ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
	}

	$uploaded = wp_handle_upload(
		$file,
		array(
			'test_form' => false,
		)
	);

	if ( isset( $uploaded['error'] ) && $uploaded['error'] ) {
		$errors[] = $uploaded['error'];
		return '';
	}

	if ( empty( $uploaded['url'] ) ) {
		$errors[] = __( 'No hemos podido procesar uno de los archivos subidos.', 'fichas-profesionales' );
		return '';
	}

	return $uploaded['url'];
}

function fichas_profesionales_handle_order_completed( $order_id ) {
	if ( ! function_exists( 'wc_get_order' ) ) {
		return;
	}

	$order = wc_get_order( $order_id );

	if ( ! $order ) {
		return;
	}

	$user_id = $order->get_user_id();

	if ( ! $user_id ) {
		return;
	}

	$plan = get_user_meta( $user_id, 'fp_membership_plan', true );

	if ( ! $plan ) {
		return;
	}

	$duration = 0;

	if ( 'quarterly' === $plan ) {
		$duration = 3;
	} elseif ( 'semiannual' === $plan ) {
		$duration = 6;
	} elseif ( 'annual' === $plan ) {
		$duration = 12;
	}

	if ( $duration <= 0 ) {
		return;
	}

	$timestamp_now = current_time( 'timestamp' );
	$expires       = gmdate( 'Y-m-d', strtotime( '+' . $duration . ' months', $timestamp_now ) );

	update_user_meta( $user_id, 'fp_membership_expires', $expires );

	$profile_id = (int) get_user_meta( $user_id, 'fp_profile_id', true );

	if ( $profile_id && 'fichas_profesionales_profile' === get_post_type( $profile_id ) ) {
		$post_data = array(
			'ID'          => $profile_id,
			'post_status' => 'publish',
		);
		wp_update_post( $post_data );
	} else {
		$first_name = get_user_meta( $user_id, 'first_name', true );
		$last_name  = get_user_meta( $user_id, 'last_name', true );
		$name       = trim( $first_name . ' ' . $last_name );

		if ( '' === $name ) {
			$user = get_userdata( $user_id );
			if ( $user && isset( $user->user_login ) ) {
				$name = $user->user_login;
			}
		}

		$post_id = wp_insert_post(
			array(
				'post_type'   => 'fichas_profesionales_profile',
				'post_title'  => $name,
				'post_status' => 'publish',
				'post_author' => $user_id,
			)
		);

		if ( $post_id && ! is_wp_error( $post_id ) ) {
			update_user_meta( $user_id, 'fp_profile_id', $post_id );
			$profile_id = $post_id;
		}
	}

	$email = '';

	$user_data = get_userdata( $user_id );

	if ( $user_data ) {
		$email = $user_data->user_email;
	}

	if ( ! $email && $order ) {
		$email = $order->get_billing_email();
	}

	if ( $email && is_email( $email ) ) {
		$profile_url = $profile_id ? get_permalink( $profile_id ) : '';
		$subject     = __( 'Tu ficha profesional ya está activa', 'fichas-profesionales' );
		$message     = __( 'Gracias por completar tu membresía. Tu ficha profesional ya está activa en el directorio.', 'fichas-profesionales' );

		if ( $profile_url ) {
			$message .= "\n\n" . sprintf( __( 'Puedes verla y compartirla desde: %s', 'fichas-profesionales' ), $profile_url );
		}

		wp_mail( $email, $subject, $message );
	}
}

add_action( 'woocommerce_order_status_completed', 'fichas_profesionales_handle_order_completed', 10, 1 );

function fichas_profesionales_profile_content( $content ) {
	if ( ! is_singular( 'fichas_profesionales_profile' ) || ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}

	global $post;

	if ( ! $post || 'fichas_profesionales_profile' !== $post->post_type ) {
		return $content;
	}

	$user_id = (int) $post->post_author;

	if ( $user_id <= 0 ) {
		return $content;
	}

	$password_set = (int) get_user_meta( $user_id, 'fp_password_set', true );
	$password_msg = '';
	$fp_action    = isset( $_POST['fp_action'] ) ? sanitize_text_field( wp_unslash( $_POST['fp_action'] ) ) : '';

	if ( is_user_logged_in() && get_current_user_id() === $user_id && 1 !== $password_set && 'POST' === $_SERVER['REQUEST_METHOD'] && 'set_password' === $fp_action ) {
		if ( ! isset( $_POST['fp_set_password_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['fp_set_password_nonce'] ) ), 'fp_set_password' ) ) {
			$password_msg = __( 'Error de seguridad al cambiar la contraseña.', 'fichas-profesionales' );
		} else {
			$new_password     = isset( $_POST['fp_new_password'] ) ? wp_unslash( $_POST['fp_new_password'] ) : '';
			$confirm_password = isset( $_POST['fp_confirm_password'] ) ? wp_unslash( $_POST['fp_confirm_password'] ) : '';

			if ( '' === $new_password || '' === $confirm_password ) {
				$password_msg = __( 'Debes introducir y confirmar la nueva contraseña.', 'fichas-profesionales' );
			} elseif ( $new_password !== $confirm_password ) {
				$password_msg = __( 'Las contraseñas no coinciden.', 'fichas-profesionales' );
			} else {
				$update_result = wp_update_user(
					array(
						'ID'        => $user_id,
						'user_pass' => $new_password,
					)
				);

				if ( is_wp_error( $update_result ) ) {
					$password_msg = __( 'No se ha podido actualizar la contraseña.', 'fichas-profesionales' );
				} else {
					update_user_meta( $user_id, 'fp_password_set', 1 );
					$password_set = 1;
					$password_msg = __( 'Tu contraseña se ha actualizado correctamente.', 'fichas-profesionales' );
				}
			}
		}
	}

	$first_name = get_user_meta( $user_id, 'first_name', true );
	$last_name  = get_user_meta( $user_id, 'last_name', true );
	$profession = get_user_meta( $user_id, 'profession', true );
	$phone      = get_user_meta( $user_id, 'phone', true );
	$city       = get_user_meta( $user_id, 'city', true );
	$region     = get_user_meta( $user_id, 'region', true );
	$website         = get_user_meta( $user_id, 'website', true );
	$instagram       = get_user_meta( $user_id, 'instagram', true );
	$facebook        = get_user_meta( $user_id, 'facebook', true );
	$youtube         = get_user_meta( $user_id, 'youtube', true );
	$language        = get_user_meta( $user_id, 'language', true );
	$search_keywords = get_user_meta( $user_id, 'search_keywords', true );
	$linkedin        = get_user_meta( $user_id, 'linkedin', true );
	$tiktok          = get_user_meta( $user_id, 'tiktok', true );
	$demo_video      = get_user_meta( $user_id, 'demo_video', true );
	$cv_url          = get_user_meta( $user_id, 'fp_cv_url', true );

	$user = get_userdata( $user_id );

	if ( $user ) {
		$email = $user->user_email;
	} else {
		$email = '';
	}

	$full_name = trim( $first_name . ' ' . $last_name );

	if ( '' === $full_name && $user ) {
		$full_name = $user->user_login;
	}

	$featured_image = '';
	$main_image_url = get_user_meta( $user_id, 'fp_main_image_url', true );

	if ( '' !== $main_image_url ) {
		$featured_image = '<img src="' . esc_url( $main_image_url ) . '" class="fp-profile-main-image" alt="' . esc_attr( $full_name ) . '" />';
	} elseif ( has_post_thumbnail( $post->ID ) ) {
		$featured_image = get_the_post_thumbnail(
			$post->ID,
			'large',
			array(
				'class' => 'fp-profile-main-image',
				'alt'   => esc_attr( $full_name ),
			)
		);
	}

	$gallery_html     = '';
	$gallery_urls_raw = get_user_meta( $user_id, 'fp_gallery_image_urls', true );
	$gallery_urls     = array();

	if ( is_array( $gallery_urls_raw ) ) {
		$gallery_urls = array_slice( $gallery_urls_raw, 0, 4 );
	}

	if ( ! empty( $gallery_urls ) ) {
		$gallery_html .= '<div class="fp-profile-gallery">';
		foreach ( $gallery_urls as $url ) {
			$gallery_html .= '<div class="fp-gallery-item"><img src="' . esc_url( $url ) . '" class="fp-gallery-image" alt="' . esc_attr( $full_name ) . '" /></div>';
		}
		$gallery_html .= '</div>';
	}

	ob_start();

	echo '<style>';
	echo '.fp-profile-wrapper{display:flex;flex-wrap:wrap;gap:2rem;align-items:flex-start;background:#ffffff;border-radius:18px;box-shadow:0 10px 30px rgba(15,23,42,0.06);padding:2rem;box-sizing:border-box;max-width:1080px;margin:0 auto;}';
	echo '.fp-profile-image{flex:0 0 280px;max-width:100%;}';
	echo '.fp-profile-main-image{width:100%;height:auto;border-radius:18px;box-shadow:0 18px 40px rgba(15,23,42,0.22);object-fit:cover;}';
	echo '.fp-profile-details{flex:1 1 260px;min-width:260px;}';
	echo '.fp-profile-details h2{margin:0 0 1rem;font-size:1.4rem;letter-spacing:.05em;text-transform:uppercase;color:#64748b;}';
	echo '.fp-profile-details ul{margin:0 0 1.25rem 1.25rem;padding:0;list-style:disc;}';
	echo '.fp-profile-details li{margin-bottom:0.4rem;line-height:1.5;color:#0f172a;}';
	echo '.fp-profile-details li strong{color:#334155;}';
	echo '.fp-profile-details a{color:#0f766e;text-decoration:none;}';
	echo '.fp-profile-details a:hover{color:#115e59;text-decoration:underline;}';
	echo '.fp-profile-gallery{display:flex;flex-wrap:wrap;gap:1rem;margin-top:1.75rem;}';
	echo '.fp-gallery-item{flex:0 0 165px;max-width:48%;}';
	echo '.fp-gallery-image{width:100%;height:auto;border-radius:10px;box-shadow:0 10px 24px rgba(15,23,42,0.14);object-fit:cover;}';
	echo '.fp-password-box{margin:0 0 2rem;max-width:1080px;margin-left:auto;margin-right:auto;padding:1.25rem 1.5rem;border:1px solid #e2e8f0;border-radius:12px;background:#f8fafc;}';
	echo '.fp-password-box p{margin:0 0 0.75rem;line-height:1.5;color:#0f172a;}';
	echo '@media (max-width:960px){.fp-profile-wrapper{padding:1.5rem;}.fp-profile-image{flex:0 0 240px;}}';
	echo '@media (max-width:768px){.fp-profile-wrapper{flex-direction:column;padding:1.25rem;}.fp-profile-image,.fp-profile-details{flex:1 1 100%;}.fp-gallery-item{flex:0 0 48%;}}';
	echo '@media (max-width:480px){.fp-profile-wrapper{padding:1rem;gap:1.5rem;}.fp-profile-details h2{font-size:1.2rem;}.fp-gallery-item{flex:0 0 100%;max-width:100%;}}';
	echo '</style>';

	if ( is_user_logged_in() && get_current_user_id() === $user_id && 1 !== $password_set ) {
		echo '<div class="fp-password-box">';
		if ( '' !== $password_msg ) {
			echo '<p>' . esc_html( $password_msg ) . '</p>';
		} else {
			echo '<p>' . esc_html__( 'Antes de continuar, por favor crea una contraseña para tu cuenta.', 'fichas-profesionales' ) . '</p>';
		}

		echo '<form method="post">';
		wp_nonce_field( 'fp_set_password', 'fp_set_password_nonce' );
		echo '<input type="hidden" name="fp_action" value="set_password" />';
		echo '<p>';
		echo '<label for="fp_new_password">' . esc_html__( 'Nueva contraseña', 'fichas-profesionales' ) . '</label><br />';
		echo '<input type="password" id="fp_new_password" name="fp_new_password" required />';
		echo '</p>';
		echo '<p>';
		echo '<label for="fp_confirm_password">' . esc_html__( 'Confirmar contraseña', 'fichas-profesionales' ) . '</label><br />';
		echo '<input type="password" id="fp_confirm_password" name="fp_confirm_password" required />';
		echo '</p>';
		echo '<p><button type="submit">' . esc_html__( 'Guardar contraseña', 'fichas-profesionales' ) . '</button></p>';
		echo '</form>';
		echo '</div>';
	}

	echo '<div class="fp-profile-wrapper">';

	if ( '' !== $featured_image ) {
		echo '<div class="fp-profile-image">' . $featured_image . '</div>';
	}

	echo '<div class="fp-profile-details">';
	echo '<h2>' . esc_html__( 'Ficha Profesional', 'fichas-profesionales' ) . '</h2>';

	echo '<ul>';

	if ( '' !== $full_name ) {
		echo '<li><strong>' . esc_html__( 'Nombre:', 'fichas-profesionales' ) . '</strong> ' . esc_html( $full_name ) . '</li>';
	}

	if ( '' !== $profession ) {
		echo '<li><strong>' . esc_html__( 'Profesión:', 'fichas-profesionales' ) . '</strong> ' . esc_html( $profession ) . '</li>';
	}

	if ( '' !== $language ) {
		echo '<li><strong>' . esc_html__( 'Idiomas:', 'fichas-profesionales' ) . '</strong> ' . esc_html( $language ) . '</li>';
	}

	if ( '' !== $city || '' !== $region ) {
		$location_parts = array();
		if ( '' !== $city ) {
			$location_parts[] = $city;
		}
		if ( '' !== $region ) {
			$location_parts[] = $region;
		}
		echo '<li><strong>' . esc_html__( 'Ubicación:', 'fichas-profesionales' ) . '</strong> ' . esc_html( implode( ', ', $location_parts ) ) . '</li>';
	}

	if ( '' !== $phone ) {
		echo '<li><strong>' . esc_html__( 'Teléfono:', 'fichas-profesionales' ) . '</strong> ' . esc_html( $phone ) . '</li>';
	}

	if ( '' !== $email ) {
		echo '<li><strong>' . esc_html__( 'Correo:', 'fichas-profesionales' ) . '</strong> <a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a></li>';
	}

	if ( '' !== $website ) {
		echo '<li><strong>' . esc_html__( 'Sitio web:', 'fichas-profesionales' ) . '</strong> <a href="' . esc_url( $website ) . '" target="_blank" rel="noopener noreferrer">' . esc_html( $website ) . '</a></li>';
	}

	if ( '' !== $instagram ) {
		echo '<li><strong>' . esc_html__( 'Instagram:', 'fichas-profesionales' ) . '</strong> ' . esc_html( $instagram ) . '</li>';
	}

	if ( '' !== $facebook ) {
		echo '<li><strong>' . esc_html__( 'Facebook:', 'fichas-profesionales' ) . '</strong> ' . esc_html( $facebook ) . '</li>';
	}

	if ( '' !== $youtube ) {
		echo '<li><strong>' . esc_html__( 'YouTube:', 'fichas-profesionales' ) . '</strong> ' . esc_html( $youtube ) . '</li>';
	}

	if ( '' !== $linkedin ) {
		echo '<li><strong>' . esc_html__( 'LinkedIn:', 'fichas-profesionales' ) . '</strong> ' . esc_html( $linkedin ) . '</li>';
	}

	if ( '' !== $tiktok ) {
		echo '<li><strong>' . esc_html__( 'TikTok:', 'fichas-profesionales' ) . '</strong> ' . esc_html( $tiktok ) . '</li>';
	}

	if ( '' !== $demo_video ) {
		echo '<li><strong>' . esc_html__( 'Vídeo demo:', 'fichas-profesionales' ) . '</strong> <a href="' . esc_url( $demo_video ) . '" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Ver vídeo', 'fichas-profesionales' ) . '</a></li>';
	}

	if ( '' !== $cv_url ) {
		echo '<li><strong>' . esc_html__( 'Currículum:', 'fichas-profesionales' ) . '</strong> <a href="' . esc_url( $cv_url ) . '" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Descargar', 'fichas-profesionales' ) . '</a></li>';
	}

	if ( '' !== $search_keywords ) {
		echo '<li><strong>' . esc_html__( 'Palabras clave:', 'fichas-profesionales' ) . '</strong> ' . esc_html( $search_keywords ) . '</li>';
	}

	echo '</ul>';

	echo apply_filters( 'fichas_profesionales_profile_extra_content', '' );

	echo '</div>';

	echo '</div>';

	if ( '' !== $gallery_html ) {
		echo $gallery_html;
	}

	return ob_get_clean();
}

function fichas_profesionales_activate() {
	if ( ! wp_next_scheduled( 'fichas_profesionales_daily_cron' ) ) {
		wp_schedule_event( time(), 'daily', 'fichas_profesionales_daily_cron' );
	}
}

register_activation_hook( __FILE__, 'fichas_profesionales_activate' );

function fichas_profesionales_deactivate() {
	wp_clear_scheduled_hook( 'fichas_profesionales_daily_cron' );
}

register_deactivation_hook( __FILE__, 'fichas_profesionales_deactivate' );

add_filter( 'the_content', 'fichas_profesionales_profile_content', 20 );

function fichas_profesionales_check_memberships() {
	$timestamp = current_time( 'timestamp' );
	$today     = gmdate( 'Y-m-d', $timestamp );
	$soon_date = gmdate( 'Y-m-d', strtotime( '+7 days', $timestamp ) );

	$expired_users = get_users(
		array(
			'meta_query' => array(
				array(
					'key'     => 'fp_membership_expires',
					'value'   => $today,
					'compare' => '<=',
					'type'    => 'DATE',
				),
			),
		)
	);

	if ( ! empty( $expired_users ) ) {
		foreach ( $expired_users as $user ) {
			$user_id  = $user->ID;
			$notified = get_user_meta( $user_id, 'fp_membership_notified_expired', true );

			if ( $notified ) {
				continue;
			}

			$profile_id = (int) get_user_meta( $user_id, 'fp_profile_id', true );

			if ( $profile_id && 'fichas_profesionales_profile' === get_post_type( $profile_id ) ) {
				$post_data = array(
					'ID'          => $profile_id,
					'post_status' => 'draft',
				);
				wp_update_post( $post_data );
			}

			$email = $user->user_email;

			if ( $email && is_email( $email ) ) {
				$subject = __( 'Tu membresía ha expirado', 'fichas-profesionales' );
				$message = __( 'Tu membresía ha expirado. Por favor, accede a tu cuenta para renovarla y mantener tu ficha publicada.', 'fichas-profesionales' );
				wp_mail( $email, $subject, $message );
			}

			update_user_meta( $user_id, 'fp_membership_status', 'expired' );
			update_user_meta( $user_id, 'fp_membership_notified_expired', 1 );
		}
	}

	$soon_users = get_users(
		array(
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key'     => 'fp_membership_expires',
					'value'   => $soon_date,
					'compare' => '=',
					'type'    => 'DATE',
				),
				array(
					'key'     => 'fp_membership_notified_soon',
					'compare' => 'NOT EXISTS',
				),
			),
		)
	);

	if ( empty( $soon_users ) ) {
		return;
	}

	foreach ( $soon_users as $user ) {
		$user_id = $user->ID;
		$email   = $user->user_email;

		if ( $email && is_email( $email ) ) {
			$subject = __( 'Tu membresía está a punto de expirar', 'fichas-profesionales' );
			$message = __( 'Tu membresía está a punto de expirar. Renueva tu plan para que tu ficha siga visible en el directorio. Si dispones de un cupón de renovación, podrás aplicarlo durante la compra.', 'fichas-profesionales' );
			wp_mail( $email, $subject, $message );
		}

		update_user_meta( $user_id, 'fp_membership_notified_soon', 1 );
	}
}

add_action( 'fichas_profesionales_daily_cron', 'fichas_profesionales_check_memberships' );

function fichas_profesionales_redirect_after_checkout( $order_id ) {
	if ( ! function_exists( 'wc_get_order' ) ) {
		return;
	}

	$order = wc_get_order( $order_id );

	if ( ! $order ) {
		return;
	}

	$user_id = $order->get_user_id();

	if ( ! $user_id || ! is_user_logged_in() || get_current_user_id() !== $user_id ) {
		return;
	}

	$profile_id = (int) get_user_meta( $user_id, 'fp_profile_id', true );

	if ( $profile_id && 'fichas_profesionales_profile' === get_post_type( $profile_id ) && 'publish' === get_post_status( $profile_id ) ) {
		$url = get_permalink( $profile_id );
		if ( $url ) {
			wp_safe_redirect( $url );
			exit;
		}
	}
}

add_action( 'woocommerce_thankyou', 'fichas_profesionales_redirect_after_checkout', 20, 1 );
