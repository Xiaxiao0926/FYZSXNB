<?php
/**
 * Plugin Name: FYZSXNB Translation Pairs
 * Description: Explicit metadata-driven translation grouping and resolver for FYZSXNB multilingual content.
 *              UI V2 0.4.0 — v0.1.2 (object-level REST authorization, existing group immutability, readback-verified rollback).
 * Version: 0.1.2
 * Author: FYZSXNB Engineering
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'FYZSXNB_TRANSLATION_PAIRS_VERSION', '0.1.2' );
define( 'FYZSXNB_TRANSLATION_RU_CAT', 54 );

/* ---------------------------------------------------------------------------
 * Meta Registration (show_in_rest => false: protect against raw REST bypass)
 * ------------------------------------------------------------------------- */

function fyzsxnb_translation_register_meta() {
	$auth = function () {
		return current_user_can( 'edit_posts' );
	};

	register_post_meta(
		'post',
		'_fyz_translation_group',
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => false, // Enforce controlled REST endpoints only
			'default'           => '',
			'sanitize_callback' => 'fyzsxnb_translation_sanitize_group',
			'auth_callback'     => $auth,
		)
	);
}
add_action( 'init', 'fyzsxnb_translation_register_meta' );

/**
 * Validate translation group format strictly.
 * Format: fyz-tp-[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}
 *
 * @param mixed $value Input group value.
 * @return string Validated group string or empty string.
 */
function fyzsxnb_translation_sanitize_group( $value ) {
	$v = strtolower( trim( (string) $value ) );
	if ( '' === $v ) {
		return '';
	}
	if ( preg_match( '/^fyz-tp-[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/', $v ) ) {
		return $v;
	}
	return '';
}

/**
 * Strict syntax check (does NOT coerce invalid string to empty).
 *
 * @param string $value Group string.
 * @return bool True if valid UUID group format.
 */
function fyzsxnb_translation_is_valid_group_format( $value ) {
	$v = strtolower( trim( (string) $value ) );
	return (bool) preg_match( '/^fyz-tp-[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/', $v );
}

/**
 * Generate a new UUIDv4-based translation group identifier.
 *
 * @return string Formatted group ID.
 */
function fyzsxnb_translation_generate_group() {
	if ( function_exists( 'wp_generate_uuid4' ) ) {
		$uuid = wp_generate_uuid4();
	} else {
		$data = random_bytes( 16 );
		$data[6] = chr( ord( $data[6] ) & 0x0f | 0x40 );
		$data[8] = chr( ord( $data[8] ) & 0x3f | 0x80 );
		$uuid = vsprintf( '%s%s-%s-%s-%s-%s%s%s', str_split( bin2hex( $data ), 4 ) );
	}
	return 'fyz-tp-' . strtolower( $uuid );
}

/**
 * Get the translation group ID of a given post.
 *
 * @param int $post_id Post ID.
 * @return string Group ID or empty string.
 */
function fyzsxnb_translation_get_group( $post_id ) {
	return strtolower( trim( (string) get_post_meta( (int) $post_id, '_fyz_translation_group', true ) ) );
}

/**
 * Restore group with read-back verification.
 *
 * @param int    $post_id Post ID.
 * @param string $previous_group Original group string.
 * @return bool True if restored and verified.
 */
function fyzsxnb_translation_restore_group( $post_id, $previous_group ) {
	$pid = (int) $post_id;
	$prev = strtolower( trim( (string) $previous_group ) );

	if ( '' === $prev ) {
		delete_post_meta( $pid, '_fyz_translation_group' );
	} else {
		update_post_meta( $pid, '_fyz_translation_group', $prev );
	}

	$actual = fyzsxnb_translation_get_group( $pid );
	return ( $actual === $prev );
}

/**
 * Get all post objects belonging to a translation group without truncation.
 *
 * @param string $group Group ID.
 * @param string $post_status Post status (default 'publish').
 * @return WP_Post[] Array of post objects.
 */
function fyzsxnb_translation_get_group_members( $group, $post_status = 'publish' ) {
	if ( ! fyzsxnb_translation_is_valid_group_format( $group ) ) {
		return array();
	}

	$query = new WP_Query(
		array(
			'post_type'              => 'post',
			'post_status'            => $post_status,
			'posts_per_page'         => -1, // No truncation limit
			'meta_key'               => '_fyz_translation_group',
			'meta_value'             => strtolower( trim( $group ) ),
			'no_found_rows'          => true,
			'update_post_term_cache' => true,
			'update_post_meta_cache' => true,
		)
	);

	return $query->posts;
}

/**
 * Validate all invariants for a translation group.
 *
 * Invariants:
 *  - INV-01: Max 1 published EN and max 1 published RU.
 *  - INV-02: No duplicate locales.
 *  - INV-03: RU member must have _fyz_content_language='ru' and category 54.
 *  - INV-04: EN member must have _fyz_content_language='en' and NOT category 54.
 *  - INV-05: Post status must be 'publish'.
 *  - INV-06: No unknown/empty locale members.
 *
 * @param string $group Group ID.
 * @return array Array with 'valid' (bool), 'errors' (string[]), 'members' (WP_Post[]).
 */
function fyzsxnb_translation_validate_group( $group ) {
	if ( ! fyzsxnb_translation_is_valid_group_format( $group ) ) {
		return array(
			'valid'   => false,
			'errors'  => array( 'invalid_group_format' ),
			'members' => array(),
		);
	}

	$members = fyzsxnb_translation_get_group_members( $group, 'any' );
	$errors  = array();
	$seen_locales = array();

	foreach ( $members as $p ) {
		$pid = (int) $p->ID;

		if ( 'publish' !== $p->post_status ) {
			$errors[] = 'member_' . $pid . '_not_published';
			continue;
		}

		$lang = strtolower( trim( (string) get_post_meta( $pid, '_fyz_content_language', true ) ) );
		if ( in_array( $lang, array( 'en', 'en-us', 'en-gb' ), true ) ) {
			$norm_lang = 'en';
		} elseif ( in_array( $lang, array( 'ru', 'ru-ru' ), true ) ) {
			$norm_lang = 'ru';
		} else {
			$norm_lang = '';
		}

		if ( '' === $norm_lang ) {
			$errors[] = 'member_' . $pid . '_unknown_locale';
			continue;
		}

		if ( isset( $seen_locales[ $norm_lang ] ) ) {
			$errors[] = 'duplicate_locale_' . $norm_lang;
		}
		$seen_locales[ $norm_lang ] = $pid;

		if ( 'ru' === $norm_lang ) {
			if ( ! has_category( FYZSXNB_TRANSLATION_RU_CAT, $pid ) ) {
				$errors[] = 'ru_member_' . $pid . '_missing_cat54';
			}
		} elseif ( 'en' === $norm_lang ) {
			if ( has_category( FYZSXNB_TRANSLATION_RU_CAT, $pid ) ) {
				$errors[] = 'en_member_' . $pid . '_has_cat54';
			}
		}
	}

	return array(
		'valid'   => empty( $errors ),
		'errors'  => $errors,
		'members' => $members,
	);
}

/**
 * Resolve the translation counterpart of a post in target locale.
 *
 * @param int    $post_id Post ID.
 * @param string $target_locale Target locale ('en' | 'ru' | 'en-US' | 'ru-RU').
 * @return WP_Post|null Matching published counterpart post or null.
 */
function fyzsxnb_translation_resolve( $post_id, $target_locale ) {
	$group = fyzsxnb_translation_get_group( $post_id );
	if ( '' === $group ) {
		return null;
	}

	$target = strtolower( trim( (string) $target_locale ) );
	if ( in_array( $target, array( 'en', 'en-us', 'en-gb' ), true ) ) {
		$target_normalized = 'en';
	} elseif ( in_array( $target, array( 'ru', 'ru-ru' ), true ) ) {
		$target_normalized = 'ru';
	} else {
		return null;
	}

	$validation = fyzsxnb_translation_validate_group( $group );
	if ( ! $validation['valid'] ) {
		return null; // Group conflict / invalidity -> do not guess
	}

	foreach ( $validation['members'] as $member ) {
		$mid = (int) $member->ID;
		if ( $mid === (int) $post_id ) {
			continue;
		}

		$mlang = strtolower( trim( (string) get_post_meta( $mid, '_fyz_content_language', true ) ) );
		if ( in_array( $mlang, array( 'en', 'en-us', 'en-gb' ), true ) ) {
			$mlang = 'en';
		} elseif ( in_array( $mlang, array( 'ru', 'ru-ru' ), true ) ) {
			$mlang = 'ru';
		}

		if ( $mlang === $target_normalized ) {
			return $member;
		}
	}

	return null;
}

/**
 * Pair two posts with existing-group immutability, format validation, and readback-verified rollback.
 *
 * @param int         $post_a_id First post ID.
 * @param int         $post_b_id Second post ID.
 * @param string|null $group_id Optional pre-determined group ID.
 * @return array Array with 'success' (bool), 'group' (string), 'error' (string).
 */
function fyzsxnb_translation_pair_posts( $post_a_id, $post_b_id, $group_id = null ) {
	$post_a_id = (int) $post_a_id;
	$post_b_id = (int) $post_b_id;

	if ( $post_a_id <= 0 || $post_b_id <= 0 || $post_a_id === $post_b_id ) {
		return array( 'success' => false, 'error' => 'invalid_post_ids' );
	}

	$post_a = get_post( $post_a_id );
	$post_b = get_post( $post_b_id );

	if ( ! $post_a || ! $post_b ) {
		return array( 'success' => false, 'error' => 'post_not_found' );
	}

	// Must be published posts
	if ( 'publish' !== $post_a->post_status || 'publish' !== $post_b->post_status ) {
		return array( 'success' => false, 'error' => 'post_not_published' );
	}

	$lang_a = strtolower( trim( (string) get_post_meta( $post_a_id, '_fyz_content_language', true ) ) );
	$lang_b = strtolower( trim( (string) get_post_meta( $post_b_id, '_fyz_content_language', true ) ) );

	$norm_a = in_array( $lang_a, array( 'en', 'en-us', 'en-gb' ), true ) ? 'en' : ( in_array( $lang_a, array( 'ru', 'ru-ru' ), true ) ? 'ru' : '' );
	$norm_b = in_array( $lang_b, array( 'en', 'en-us', 'en-gb' ), true ) ? 'en' : ( in_array( $lang_b, array( 'ru', 'ru-ru' ), true ) ? 'ru' : '' );

	if ( '' === $norm_a || '' === $norm_b ) {
		return array( 'success' => false, 'error' => 'missing_or_unknown_language_meta' );
	}

	if ( $norm_a === $norm_b ) {
		return array( 'success' => false, 'error' => 'same_language_pairing_prohibited' );
	}

	$en_id = ( 'en' === $norm_a ) ? $post_a_id : $post_b_id;
	$ru_id = ( 'ru' === $norm_a ) ? $post_a_id : $post_b_id;

	if ( ! has_category( FYZSXNB_TRANSLATION_RU_CAT, $ru_id ) ) {
		return array( 'success' => false, 'error' => 'ru_post_missing_cat54' );
	}
	if ( has_category( FYZSXNB_TRANSLATION_RU_CAT, $en_id ) ) {
		return array( 'success' => false, 'error' => 'en_post_has_cat54' );
	}

	$existing_a = fyzsxnb_translation_get_group( $post_a_id );
	$existing_b = fyzsxnb_translation_get_group( $post_b_id );

	// Existing Group Format Validation (No destructive clear of malformed values)
	if ( '' !== $existing_a && ! fyzsxnb_translation_is_valid_group_format( $existing_a ) ) {
		return array( 'success' => false, 'error' => 'invalid_existing_group_a' );
	}
	if ( '' !== $existing_b && ! fyzsxnb_translation_is_valid_group_format( $existing_b ) ) {
		return array( 'success' => false, 'error' => 'invalid_existing_group_b' );
	}

	// Existing Group Immutability / Conflict Check
	if ( '' !== $existing_a && '' !== $existing_b && $existing_a !== $existing_b ) {
		return array( 'success' => false, 'error' => 'conflicting_existing_groups' );
	}

	if ( $group_id ) {
		if ( ! fyzsxnb_translation_is_valid_group_format( $group_id ) ) {
			return array( 'success' => false, 'error' => 'invalid_provided_group_id' );
		}
		$req = strtolower( trim( $group_id ) );
		// Silent rebind protection: requested group must match existing non-empty group
		if ( '' !== $existing_a && $existing_a !== $req ) {
			return array( 'success' => false, 'error' => 'existing_group_mismatch' );
		}
		if ( '' !== $existing_b && $existing_b !== $req ) {
			return array( 'success' => false, 'error' => 'existing_group_mismatch' );
		}
		$target_group = $req;
	} elseif ( '' !== $existing_a ) {
		$target_group = $existing_a;
	} elseif ( '' !== $existing_b ) {
		$target_group = $existing_b;
	} else {
		$target_group = fyzsxnb_translation_generate_group();
	}

	// Pre-check target group existing members (Simulated Add)
	$existing_members = fyzsxnb_translation_get_group_members( $target_group, 'publish' );
	foreach ( $existing_members as $m ) {
		$mid = (int) $m->ID;
		if ( $mid === $post_a_id || $mid === $post_b_id ) {
			continue;
		}
		$mlang = strtolower( trim( (string) get_post_meta( $mid, '_fyz_content_language', true ) ) );
		$mnorm = in_array( $mlang, array( 'en', 'en-us', 'en-gb' ), true ) ? 'en' : ( in_array( $mlang, array( 'ru', 'ru-ru' ), true ) ? 'ru' : '' );
		if ( 'en' === $mnorm ) {
			return array( 'success' => false, 'error' => 'target_group_already_has_en_member' );
		}
		if ( 'ru' === $mnorm ) {
			return array( 'success' => false, 'error' => 'target_group_already_has_ru_member' );
		}
	}

	// Atomic Compensatory Write with Read-back Verification
	$old_a = $existing_a;
	$old_b = $existing_b;

	// Step 1: Write Post A
	$up_a = update_post_meta( $post_a_id, '_fyz_translation_group', $target_group );
	if ( false === $up_a && fyzsxnb_translation_get_group( $post_a_id ) !== $target_group ) {
		return array( 'success' => false, 'error' => 'write_failed_post_a' );
	}

	// Step 2: Write Post B
	$up_b = update_post_meta( $post_b_id, '_fyz_translation_group', $target_group );
	if ( false === $up_b && fyzsxnb_translation_get_group( $post_b_id ) !== $target_group ) {
		// Rollback A with read-back verification
		$rb_a = fyzsxnb_translation_restore_group( $post_a_id, $old_a );
		if ( $rb_a ) {
			return array( 'success' => false, 'error' => 'write_failed_post_b_rollback_verified' );
		}
		return array( 'success' => false, 'error' => 'write_failed_post_b_ROLLBACK_FAILED' );
	}

	// Step 3: Final verification of group invariants
	$final_check = fyzsxnb_translation_validate_group( $target_group );
	if ( ! $final_check['valid'] ) {
		// Rollback both with read-back verification
		$rb_a = fyzsxnb_translation_restore_group( $post_a_id, $old_a );
		$rb_b = fyzsxnb_translation_restore_group( $post_b_id, $old_b );
		if ( $rb_a && $rb_b ) {
			return array( 'success' => false, 'error' => 'final_check_failed_rollback_verified', 'details' => $final_check['errors'] );
		}
		return array( 'success' => false, 'error' => 'final_check_failed_ROLLBACK_FAILED', 'details' => $final_check['errors'] );
	}

	return array(
		'success' => true,
		'group'   => $target_group,
		'en_id'   => $en_id,
		'ru_id'   => $ru_id,
	);
}

/**
 * Remove translation group from a post.
 *
 * @param int $post_id Post ID.
 * @return bool True if removed.
 */
function fyzsxnb_translation_unpair_post( $post_id ) {
	$pid = (int) $post_id;
	if ( $pid <= 0 ) {
		return false;
	}
	return delete_post_meta( $pid, '_fyz_translation_group' );
}

/* ---------------------------------------------------------------------------
 * Controlled REST Endpoints (with Object-level Authorization)
 * ------------------------------------------------------------------------- */

function fyzsxnb_translation_qa_nocache() {
	nocache_headers();
	if ( ! headers_sent() ) {
		header( 'Cache-Control: no-cache, no-store, must-revalidate' );
	}
}

function fyzsxnb_translation_rest_inspect( WP_REST_Request $request ) {
	fyzsxnb_translation_qa_nocache();
	$post_id = (int) $request->get_param( 'post_id' );

	$post = get_post( $post_id );
	if ( ! $post ) {
		return new WP_Error( 'post_not_found', 'Post not found', array( 'status' => 404 ) );
	}

	$lang     = strtolower( trim( (string) get_post_meta( $post_id, '_fyz_content_language', true ) ) );
	$kind     = strtolower( trim( (string) get_post_meta( $post_id, '_fyz_content_kind', true ) ) );
	$group    = fyzsxnb_translation_get_group( $post_id );
	$cats     = wp_get_post_categories( $post_id );
	$has_54   = in_array( FYZSXNB_TRANSLATION_RU_CAT, $cats, true );

	$target_locale = ( 'ru' === $lang ) ? 'en' : 'ru';
	$counterpart   = ( '' !== $group ) ? fyzsxnb_translation_resolve( $post_id, $target_locale ) : null;
	$val           = ( '' !== $group ) ? fyzsxnb_translation_validate_group( $group ) : null;

	$response = array(
		'post_id'      => $post_id,
		'title'        => get_the_title( $post_id ),
		'slug'         => $post->post_name,
		'status'       => $post->post_status,
		'language'     => $lang,
		'kind'         => $kind,
		'categories'   => $cats,
		'has_cat54'    => $has_54,
		'group'        => $group,
		'group_valid'  => $val ? $val['valid'] : null,
		'group_errors' => $val ? $val['errors'] : array(),
		'counterpart'  => $counterpart ? array(
			'post_id'  => (int) $counterpart->ID,
			'title'    => get_the_title( $counterpart->ID ),
			'slug'     => $counterpart->post_name,
			'language' => get_post_meta( $counterpart->ID, '_fyz_content_language', true ),
		) : null,
	);

	return rest_ensure_response( $response );
}

function fyzsxnb_translation_rest_pair( WP_REST_Request $request ) {
	fyzsxnb_translation_qa_nocache();
	$en_id    = (int) $request->get_param( 'en_id' );
	$ru_id    = (int) $request->get_param( 'ru_id' );
	$group_id = $request->get_param( 'group_id' );

	$result = fyzsxnb_translation_pair_posts( $en_id, $ru_id, $group_id );
	if ( ! $result['success'] ) {
		return new WP_Error( 'pair_failed', $result['error'], array( 'status' => 400, 'details' => $result ) );
	}

	return rest_ensure_response( $result );
}

function fyzsxnb_translation_rest_unpair( WP_REST_Request $request ) {
	fyzsxnb_translation_qa_nocache();
	$post_id = (int) $request->get_param( 'post_id' );

	$removed = fyzsxnb_translation_unpair_post( $post_id );
	return rest_ensure_response( array( 'post_id' => $post_id, 'unpaired' => $removed ) );
}

add_action(
	'rest_api_init',
	function () {
		register_rest_route(
			'fyzsxnb/v1',
			'/translation-pairs/inspect',
			array(
				'methods'             => 'GET',
				'permission_callback' => function ( WP_REST_Request $request ) {
					$post_id = (int) $request->get_param( 'post_id' );
					if ( $post_id <= 0 || ! get_post( $post_id ) ) {
						return new WP_Error( 'invalid_post_id', 'post_id must be a positive integer of an existing post', array( 'status' => 400 ) );
					}
					if ( ! current_user_can( 'edit_post', $post_id ) ) {
						return new WP_Error( 'forbidden_post', 'You do not have permission to inspect this post.', array( 'status' => 403 ) );
					}
					return true;
				},
				'callback'            => 'fyzsxnb_translation_rest_inspect',
				'args'                => array(
					'post_id' => array( 'type' => 'integer', 'required' => true ),
				),
			)
		);

		register_rest_route(
			'fyzsxnb/v1',
			'/translation-pairs/pair',
			array(
				'methods'             => 'POST',
				'permission_callback' => function ( WP_REST_Request $request ) {
					$en_id = (int) $request->get_param( 'en_id' );
					$ru_id = (int) $request->get_param( 'ru_id' );
					if ( $en_id <= 0 || $ru_id <= 0 || $en_id === $ru_id ) {
						return new WP_Error( 'invalid_post_ids', 'en_id and ru_id must be distinct positive integers', array( 'status' => 400 ) );
					}
					if ( ! current_user_can( 'edit_post', $en_id ) || ! current_user_can( 'edit_post', $ru_id ) ) {
						return new WP_Error( 'pair_permission_denied', 'You do not have permission to edit both posts in the pair.', array( 'status' => 403 ) );
					}
					return true;
				},
				'callback'            => 'fyzsxnb_translation_rest_pair',
				'args'                => array(
					'en_id'    => array( 'type' => 'integer', 'required' => true ),
					'ru_id'    => array( 'type' => 'integer', 'required' => true ),
					'group_id' => array( 'type' => 'string', 'required' => false ),
				),
			)
		);

		register_rest_route(
			'fyzsxnb/v1',
			'/translation-pairs/unpair',
			array(
				'methods'             => 'POST',
				'permission_callback' => function ( WP_REST_Request $request ) {
					$post_id = (int) $request->get_param( 'post_id' );
					if ( $post_id <= 0 || ! get_post( $post_id ) ) {
						return new WP_Error( 'invalid_post_id', 'post_id must be a positive integer of an existing post', array( 'status' => 400 ) );
					}
					if ( ! current_user_can( 'edit_post', $post_id ) ) {
						return new WP_Error( 'unpair_permission_denied', 'You do not have permission to edit this post.', array( 'status' => 403 ) );
					}
					return true;
				},
				'callback'            => 'fyzsxnb_translation_rest_unpair',
				'args'                => array(
					'post_id' => array( 'type' => 'integer', 'required' => true ),
				),
			)
		);
	}
);
