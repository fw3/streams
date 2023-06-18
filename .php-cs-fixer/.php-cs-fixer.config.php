<?php
/**
 *     _______       _______
 *    / ____/ |     / /__  /
 *   / /_   | | /| / / /_ <
 *  / __/   | |/ |/ /___/ /
 * /_/      |__/|__//____/
 *
 * Flywheel3: the inertia php framework
 *
 * @category    Flywheel3
 * @package     streams
 * @author      wakaba <wakabadou@gmail.com>
 * @copyright   Copyright (c) @2019  Wakabadou (http://www.wakabadou.net/) / Project ICKX (https://ickx.jp/). All rights reserved.
 * @license     http://opensource.org/licenses/MIT The MIT License.
 *              This software is released under the MIT License.
 * @varsion     1.0.0
 */

declare(strict_types=1);

return (new PhpCsFixer\Config())
->setLineEnding("\n")
->setRiskyAllowed(true)
->setUsingCache(false)
->setRules([
    '@PSR12'                                            => true,
    //    '@PHP82Migration'                                   => true,  // @MEMO 現行での最低要件がPHP7.2のため
    'align_multiline_comment'                           => ['comment_type'  => 'phpdocs_only'],
    'array_indentation'                                 => true,
    'array_push'                                        => true,
    'array_syntax'                                      => ['syntax'    => 'short'],
    'assign_null_coalescing_to_coalesce_equal'          => false,
    'backtick_to_shell_exec'                            => true,
    'binary_operator_spaces'                            => ['default'   => 'align_single_space'],
    'blank_line_after_namespace'                        => true,
    'blank_line_after_opening_tag'                      => true,
    'blank_line_before_statement'                       => [
        'statements' => [
            'break',
            'phpdoc',
            'do',
            'exit',
            'for',
            'foreach',
            'goto',
            'if',
            'include',
            'include_once',
            'require',
            'require_once',
            'return',
            'switch',
            'throw',
            'try',
            'while',
            'yield',
            'yield_from',
        ],
    ],
    'braces'                                            => [
        'allow_single_line_anonymous_class_with_empty_body' => true,
        'allow_single_line_closure'                         => false,
        'position_after_anonymous_constructs'               => 'same',
        'position_after_control_structures'                 => 'same',
        'position_after_functions_and_oop_constructs'       => 'next',
    ],
    'cast_spaces'                                       => ['space' => 'single'],
    'class_attributes_separation'                       => [
        'elements'          => [
            'const'         => 'one',
            'method'        => 'one',
            'property'      => 'one',
            'trait_import'  => 'none',
            'case'          => 'none',
        ],
    ],
    'class_definition'                                  => [
        'inline_constructor_arguments'          => true,
        'multi_line_extends_each_single_line'   => false,
        'single_item_single_line'               => false,
        'single_line'                           => false,
        'space_before_parenthesis'              => false,
    ],
    'class_keyword_remove'                              => false,
    'combine_consecutive_issets'                        => true,
    'combine_consecutive_unsets'                        => true,
    'combine_nested_dirname'                            => true,
    'compact_nullable_typehint'                         => true,
    'concat_space'                                      => ['spacing'   => 'one'],
    'constant_case'                                     => ['case'      => 'lower'],
    'control_structure_continuation_position'           => ['position'  => 'same_line'],
    'date_time_create_from_format_call'                 => false,
    'date_time_immutable'                               => false,
    'declare_equal_normalize'                           => ['space'     => 'none'],
    'declare_parentheses'                               => true,
    'declare_strict_types'                              => true,
    'dir_constant'                                      => true,
    'doctrine_annotation_array_assignment'              => [
        'ignored_tags'  => [],
        'operator'      => '=',
    ],
    'doctrine_annotation_braces'                        => [
        'ignored_tags'  => [],
        'syntax'        => 'without_braces',
    ],
    'doctrine_annotation_indentation'                   => [
        'ignored_tags'          => [],
        'indent_mixed_lines'    => false,
    ],
    'doctrine_annotation_spaces'                        => [
        'after_argument_assignments'        => false,
        'after_array_assignments_colon'     => true,
        'after_array_assignments_equals'    => true,
        'around_commas'                     => true,
        'around_parentheses'                => true,
        'before_argument_assignments'       => false,
        'before_array_assignments_colon'    => true,
        'before_array_assignments_equals'   => true,
        'ignored_tags'                      => [],
    ],
    'echo_tag_syntax'                                   => [
        'format'                            => 'long',
        'long_function'                     => 'echo',
        'shorten_simple_statements_only'    => true,
    ],
    'elseif'                                            => true,
    'empty_loop_body'                                   => ['style' => 'semicolon'],
    'empty_loop_condition'                              => ['style' => 'for'],
    'encoding'                                          => true,
    //     'ereg_to_preg',                 // @MEMO 明示的なデフォルト挙動
    //     'error_suppression',            // @MEMO 明示的なデフォルト挙動
    //     'escape_implicit_backslashes',  // @MEMO 明示的なデフォルト挙動
    'explicit_indirect_variable'                        => false,
    'explicit_string_variable'                          => false,
    //     'final_class',                              // @MEMO 明示的なデフォルト挙動
    //     'final_internal_class',                     // @MEMO 明示的なデフォルト挙動
    //     'final_public_method_for_abstract_class',   // @MEMO 明示的なデフォルト挙動
    'fopen_flag_order'                                  => true,
    'fopen_flags'                                       => true,
    'full_opening_tag'                                  => true,
    'fully_qualified_strict_types'                      => true,
    'function_declaration'                              => [
        'closure_function_spacing'      => 'none',
        'trailing_comma_single_line'    => true,
    ],
    'function_to_constant'                              => [
        'functions' => [
            'get_called_class',
            'get_class',
            'get_class_this',
            'php_sapi_name',
            'phpversion',
            'pi',
        ],
    ],
    'function_typehint_space'                           => true,
    //     'general_phpdoc_annotation_remove', // @MEMO 明示的なデフォルト挙動
    //     'general_phpdoc_tag_rename',        // @MEMO 明示的なデフォルト挙動
    'get_class_to_class_keyword'                        => true,
    'global_namespace_import'                           => [
        'import_classes'    => false,
        'import_constants'  => false,
        'import_functions'  => false,
    ],
    'group_import'                                      => false,
    'header_comment'                                    => [
        'comment_type'  => 'PHPDoc',
        'header'        => \rtrim(\file_get_contents(\sprintf('%s/header_comment.txt', __DIR__))),
        'location'      => 'after_open',
        'separate'      => 'bottom',
    ],
    'heredoc_indentation'                               => ['indentation'   => 'same_as_start'],
    'heredoc_to_nowdoc'                                 => true,
    'implode_call'                                      => true,
    'include'                                           => true,
    'increment_style'                                   => true,
    'indentation_type'                                  => true,
    'integer_literal_case'                              => true,
    'is_null'                                           => true,
    'lambda_not_used_import'                            => true,
    'line_ending'                                       => true,
    'linebreak_after_opening_tag'                       => true,
    'list_syntax'                                       => true,
    'logical_operators'                                 => true,
    'lowercase_cast'                                    => true,
    'lowercase_keywords'                                => true,
    'lowercase_static_reference'                        => true,
    'magic_constant_casing'                             => true,
    'magic_method_casing'                               => true,
    //     'mb_str_functions', // @MEMO 明示的なデフォルト挙動
    'method_argument_space'                             => [
        'after_heredoc'                     => false,
        'keep_multiple_spaces_after_comma'  => false,
        'on_multiline'                      => 'ensure_fully_multiline',
    ],
    'method_chaining_indentation'                       => false,
    'modernize_strpos'                                  => true,
    'modernize_types_casting'                           => true,
    'multiline_comment_opening_closing'                 => true,
    'multiline_whitespace_before_semicolons'            => ['strategy'  => 'no_multi_line'],
    'native_constant_invocation'                        => [
        'exclude'       => [
            'true',
            'false',
            'null',
        ],
        'include'       => [],
        'fix_built_in'  => true,
        'scope'         => 'all',
        'strict'        => true,
    ],
    'native_function_casing'                            => true,
    'native_function_invocation'                        => [
        'exclude'   => [],
        'include'   => ['@all'],
        'scope'     => 'all',
        'strict'    => true,
    ],
    'native_function_type_declaration_casing'           => true,
    'new_with_braces'                                   => [
        'anonymous_class'   => true,
        'named_class'       => true,
    ],
    'no_alias_functions'                                => [
        'sets'  => ['@all'],
    ],
    'no_alias_language_construct_call'                  => true,
    'no_alternative_syntax'                             => [
        'fix_non_monolithic_code'   => true,
    ],
    //     'no_binary_string', // @MEMO バイナリ文字列が実装されたタイミングで判断したい
    'no_blank_lines_after_class_opening'                => true,
    'no_blank_lines_after_phpdoc'                       => true,
    'no_blank_lines_before_namespace'                   => false,
    'no_break_comment'                                  => true,
    'no_closing_tag'                                    => true,
    'no_empty_comment'                                  => true,
    'no_empty_phpdoc'                                   => true,
    'no_empty_statement'                                => true,
    'no_extra_blank_lines'                              => [
        'tokens'    => ['extra'],
    ],
    'no_homoglyph_names'                                => false,
    'no_leading_import_slash'                           => true,
    'no_leading_namespace_whitespace'                   => true,
    'no_mixed_echo_print'                               => ['use'   => 'echo'],
    'no_multiline_whitespace_around_double_arrow'       => true,
    'no_null_property_initialization'                   => true,
    'no_php4_constructor'                               => true,
    'no_short_bool_cast'                                => true,
    'no_singleline_whitespace_before_semicolons'        => true,
    'no_space_around_double_colon'                      => true,
    'no_spaces_after_function_name'                     => true,
    'no_spaces_around_offset'                           => [
        'positions' => [
            'inside',
            'outside',
        ],
    ],
    'no_spaces_inside_parenthesis'                      => true,
    'no_superfluous_elseif'                             => true,
    'no_superfluous_phpdoc_tags'                        => [
        'allow_mixed'           => false,
        'allow_unused_params'   => false,
        'remove_inheritdoc'     => false,
    ],
    'no_trailing_comma_in_list_call'                    => true,
    'no_trailing_comma_in_singleline_array'             => true,
    'no_trailing_comma_in_singleline_function_call'     => true,
    'no_trailing_whitespace'                            => true,
    'no_trailing_whitespace_in_comment'                 => true,
    'no_trailing_whitespace_in_string'                  => true,
    'no_unneeded_control_parentheses'                   => true,
    'no_unneeded_curly_braces'                          => true,
    //     'no_unneeded_final_method', // PHP7.2以降併せなので@MEMO 明示的なデフォルト挙動
    'no_unneeded_import_alias'                          => true,
    'no_unreachable_default_argument_value'             => false,
    'no_unset_cast'                                     => true,
    'no_unset_on_property'                              => false,
    'no_unused_imports'                                 => true,
    'no_useless_else'                                   => true,
    'no_useless_return'                                 => true,
    'no_useless_sprintf'                                => true,
    'no_whitespace_before_comma_in_array'               => true,
    'no_whitespace_in_blank_line'                       => true,
    'non_printable_character'                           => true,
    //     'normalize_index_brace',    // @MEMO 明示的なデフォルト挙動
    'not_operator_with_space'                           => false,
    'not_operator_with_successor_space'                 => false,
    'nullable_type_declaration_for_default_null_value'  => ['use_nullable_type_declaration' => true],
    'object_operator_without_whitespace'                => true,
    //     'octal_notation',    // @MEMO 明示的なデフォルト挙動
    'operator_linebreak'                                => [
        'only_booleans' => true,
        'position'      => 'beginning',
    ],
    // ==============================================
    // クラス定義
    // ==============================================
    'ordered_class_elements'                            => [
        'order'             => [
            'use_trait',

            'constant_public',
            'constant_protected',
            'constant_private',

            'property_public_static',
            'property_protected_static',
            'property_private_static',

            'property_public_readonly',
            'property_protected_readonly',
            'property_private_readonly',

            'property_public',
            'property_protected',
            'property_private',

            'method_public_abstract_static',
            'method_protected_abstract_static',
            'method_private_abstract_static',

            'method_public_abstract',
            'method_protected_abstract',
            'method_private_abstract',

            'method_public_static',
            'method_protected_static',
            'method_private_static',

            'construct',

            'method_public',
            'method_protected',
            'method_private',

            'magic',

            'phpunit',

            'destruct',
        ],
        'sort_algorithm'    => 'none',
    ],
    'ordered_imports'                                   => [
        'imports_order'     => [
            'class',
            'function',
            'const',
        ],
        'sort_algorithm'    => 'alpha',
    ],
    'ordered_interfaces'                                => [],      // @MEMO 'ordered_traits'と挙動を併せるため未設定
    'ordered_traits'                                    => false,   // @MEMO ヒューマンリーダブルを優先する
    // ==============================================
    // PHP Unit
    // ==============================================
    'php_unit_construct'                                => [],
    'php_unit_dedicate_assert'                          => [
        'target'    => 'newest',
    ],
    'php_unit_dedicate_assert_internal_type'            => [
        'target'    => 'newest',
    ],
    'php_unit_expectation'                              => [
        'target'    => 'newest',
    ],
    'php_unit_fqcn_annotation'                          => false,
    'php_unit_internal_class'                           => [
        'types' => [
            'normal',
            'final',
        ],
    ],
    'php_unit_method_casing'                            => ['case'  => 'camel_case'],
    'php_unit_mock'                                     => [
        'target'    => 'newest',
    ],
    'php_unit_mock_short_will_return'                   => true,
    'php_unit_namespaced'                               => [
        'target'    => 'newest',
    ],
    'php_unit_no_expectation_annotation'                => [
        'target'            => 'newest',
        'use_class_const'   => true,
    ],
    'php_unit_set_up_tear_down_visibility'              => false,
    //     'php_unit_size_class',  // @MEMO 明示的なデフォルト挙動
    'php_unit_strict'                                   => [
        'assertions'    => [
            // @MEMO 配列比較時に困難が生じるため、強制sameを行わない
            //             'assertAttributeEquals',
            //             'assertAttributeNotEquals',
            //             'assertEquals',
            //             'assertNotEquals',
        ],
    ],
    'php_unit_test_annotation'                          => [
        'style' => 'annotation', // or 'prefix',
    ],
    //     'php_unit_test_case_static_method_calls',   // @MEMO 明示的なデフォルト挙動
    //     'php_unit_test_class_requires_covers',      // @MEMO 明示的なデフォルト挙動
    // ==============================================
    // PHP Doc
    // ==============================================
    //     'phpdoc_add_missing_param_annotation',  // @MEMO 明示的なデフォルト挙動 挿入位置が不確定なため
    'phpdoc_align'                                      => [
        'align' => 'vertical',
    ],
    'phpdoc_annotation_without_dot'                     => true,
    'phpdoc_indent'                                     => true,
    'phpdoc_inline_tag_normalizer'                      => [
        'tags'  => [
            'example',
            'id',
            'internal',
            'inheritdoc',
            'inheritdocs',
            'link',
            'source',
            'toc',
            'tutorial',
        ],
    ],
    'phpdoc_line_span'                                  => [
        'const'     => 'multi',
        'method'    => 'multi',
        'property'  => 'multi',
    ],
    'phpdoc_no_access'                                  => true,
    //     'phpdoc_no_alias_tag',  // @MEMO 明示的なデフォルト挙動
    'phpdoc_no_empty_return'                            => false,
    'phpdoc_no_package'                                 => false,
    'phpdoc_no_useless_inheritdoc'                      => false,
    'phpdoc_order'                                      => true,
    'phpdoc_order_by_value'                             => [
        'annotations'   => [
            'author',
            'covers',
            'coversNothing',
            'group',
            'dataProvider',
            'property',
            'property-read',
            'property-write',
            'method',
            'throws',
            'uses',
            'internal',
            'depends',
            'requires',
        ],
    ],
    //     'phpdoc_return_self_reference', // @MEMO 明示的なデフォルト挙動
    'phpdoc_scalar'                                     => [
        'types' => [
            'boolean',
            'callback',
            'double',
            'integer',
            'real',
            'str',
        ],
    ],
    'phpdoc_separation'                                 => false,
    'phpdoc_single_line_var_spacing'                    => true,
    'phpdoc_summary'                                    => false,
    //     'phpdoc_tag_casing',        // @MEMO 明示的なデフォルト挙動
    //     'phpdoc_tag_type',          // @MEMO 明示的なデフォルト挙動
    //     'phpdoc_to_comment',        // @MEMO 明示的なデフォルト挙動
    'phpdoc_to_param_type'                              => true,
    'phpdoc_to_property_type'                           => true,
    'phpdoc_to_return_type'                             => true,
    'phpdoc_trim'                                       => true,
    'phpdoc_trim_consecutive_blank_line_separation'     => true,
    'phpdoc_types'                                      => [
        'groups'                                        => [
            'simple',
            'alias',
            'meta',
        ],
    ],
    'phpdoc_types_order'                                => [
        'null_adjustment'   => 'always_first',  // @MEMO nullableである事を最初にお伝えする
        'sort_algorithm'    => 'none',          // @MEMO 高頻度使用順で記載するケースがあるため
    ],
    'phpdoc_var_annotation_correct_order'               => true,
    'phpdoc_var_without_name'                           => true,
    'pow_to_exponentiation'                             => false,
    'protected_to_private'                              => false,
    //     'psr_autoloading',  // @MEMO 明示的なデフォルト挙動
    //     'random_api_migration', // @MEMO https://wiki.php.net/rfc/rng_extension 待ち。世界のぜりよし
    'regular_callable_call'                             => true,
    'return_assignment'                                 => true,
    'return_type_declaration'                           => [
        'space_before'  => 'none',
    ],
    'self_accessor'                                     => false,   // @MEMO interface側引数で自身を参照している場合に実装側と不整合を起こすケースがあるためfalse
    'self_static_accessor'                              => true,
    'semicolon_after_instruction'                       => true,
    'set_type_to_cast'                                  => false,
    'short_scalar_cast'                                 => true,
    'simple_to_complex_string_variable'                 => true,
    'simplified_if_return'                              => false,   // @MEMO boolキャストを付与するケースはNGなため
    'simplified_null_return'                            => false,   // @MEMO voidとnullは明確に異なる
    'single_blank_line_at_eof'                          => true,
    'single_blank_line_before_namespace'                => true,
    'single_class_element_per_statement'                => [
        'elements'  => [
            'const',
            'property',
        ],
    ],
    'single_import_per_statement'                       => true,
    'single_line_after_imports'                         => true,
    'single_line_comment_spacing'                       => true,
    'single_line_comment_style'                         => [
        'comment_types' => [
            'hash',
        ],
    ],
    'single_line_throw'                                 => true,
    'single_quote'                                      => true,
    'single_space_after_construct'                      => [
        'constructs'    => [
            'abstract',
            'as',
            'attribute',
            'break',
            'case',
            'catch',
            'class',
            'clone',
            'comment',
            'const',
            'const_import',
            'continue',
            'do',
            'echo',
            'else',
            'elseif',
            'enum',
            'extends',
            'final',
            'finally',
            'for',
            'foreach',
            'function',
            'function_import',
            'global',
            'goto',
            'if',
            'implements',
            'include',
            'include_once',
            'instanceof',
            'insteadof',
            'interface',
            'match',
            'named_argument',
            'namespace',
            'new',
            'open_tag_with_echo',
            'php_doc',
            'php_open',
            'print',
            'private',
            'protected',
            'public',
            'readonly',
            'require',
            'require_once',
            'return',
            'static',
            'switch',
            'throw',
            'trait',
            'try',
            'use',
            'use_lambda',
            'use_trait',
            'var',
            'while',
            'yield',
            'yield_from',
        ],
    ],
    'single_trait_insert_per_statement'                 => true,
    'space_after_semicolon'                             => false,
    'standardize_increment'                             => true,
    'standardize_not_equals'                            => true,
    //     'static_lambda',    // @MEMO staticの取り扱いが将来PHPで変化する可能性が高いため、今は設定しない
    'strict_comparison'                                 => true,
    'strict_param'                                      => true,
    'string_length_to_empty'                            => true,
    //     'string_line_ending',   // @MEMO 明示的なデフォルト挙動
    'switch_case_semicolon_to_colon'                    => true,
    'switch_case_space'                                 => true,
    'switch_continue_to_break'                          => true,
    'ternary_operator_spaces'                           => true,
    'ternary_to_elvis_operator'                         => false,
    'ternary_to_null_coalescing'                        => true,
    'trailing_comma_in_multiline'                       => [
        'after_heredoc' => true,
        'elements'      => [
            'arrays',
            'arguments',
            'parameters',
        ],
    ],
    'trim_array_spaces'                                 => true,
    'types_spaces'                                      => [
        'space'                 => 'none',
        'space_multiple_catch'  => null,
    ],
    'unary_operator_spaces'                             => true,
    'use_arrow_functions'                               => false,
    'visibility_required'                               => [
        'elements'  => [
            'property',
            'method',
            'const',
        ],
    ],
    'void_return'                                       => true,
    'whitespace_after_comma_in_array'                   => true,
    //     'yoda_style',   // @MEMO 明示的なデフォルト挙動
]);
