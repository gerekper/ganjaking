<?php

/**
 * SearchWP i18n.
 *
 * @package SearchWP
 * @author  Jon Christopher
 */

namespace SearchWP\Admin;

/**
 * Class i18n is responsible for defining strings for i18n.
 *
 * @since 4.0
 */
class i18n {

	public static function get() {
		return [
			'_attribute' => sprintf(
				// Translators: placeholder is the number "1"
				__( '%s Attribute', 'searchwp' ),
				'{{ searchwpPlaceholder1 }}'
			),
			'_attributes' => sprintf(
				// Translators: placeholder is the number of attributes
				__( '%s Attributes', 'searchwp' ),
				'{{ searchwpPlaceholder1 }}'
			),
			'_rule' => sprintf(
				// Translators: placeholder is the number "1"
				__( '%s Rule', 'searchwp' ),
				'{{ searchwpPlaceholder1 }}'
			),
			'_rules' => sprintf(
				// Translators: placeholder is the number of attributes
				__( '%s Rules', 'searchwp' ),
				'{{ searchwpPlaceholder1 }}'
			),
			'_admin_engine_note' => __( 'This engine will be used for WordPress Admin searches, it will search these sources:', 'searchwp' ),
			'_admin_engine_tooltip' => __( 'When searching a supported Source, this Engine will be used', 'searchwp' ),
			'_admin_engine_defined_note' => sprintf(
				// Translators: placeholder is the label of the engine.
				__( 'Already set: %s', 'searchwp' ),
				'{{ searchwpPlaceholder1 }}' // This is a sequential token to be processed on the front end.
			),
			'_alternate_indexer_note' => __( 'Alternate indexer running: leave this browser window open until the initial index has been built.', 'searchwp' ),
			'_alternate_indexer_done' => __( 'Alternate indexer in use: initial index built. This browser window can be closed, the index will be kept up to date automatically.', 'searchwp' ),
			'_attributes_choices_note' => wp_kses(
				sprintf(
					// Translators: 1st placeholder is the name of a Source, 2nd placeholder is the label of the engine.
					__( 'Choose which %1$s Attributes should be considered when searching the %2$s engine:', 'searchwp' ),
					'<strong>{{ sourceSingular }}</strong>',
					'<strong>{{ engineLabel }}</strong>'
				),
				[ 'strong' => [], ]
			),
			'_attributes_options_search_note' => wp_kses(
				sprintf(
					// Translators: 1st placeholder is the label of a Source Attribute.
					__( 'Type to search %1$s...', 'searchwp' ),
					'<strong>{{ attributeLabel }}</strong>'
				),
				[ 'strong' => [], ]
			),
			'_copy_clipboard_error' => __( 'There was an error copying to your clipboard, please manually copy and paste', 'searchwp' ),
			'_confirm_statistics_reset' => __( 'Are you sure you want to reset statistics? This cannot be undone!', 'searchwp' ),
			'_cron_note' => __( 'Potential WP-Cron issue detected, background process may not be fully functional.', 'searchwp' ),
			'_default_engine_note' => __( 'This engine will be used for native WordPress searches, it will search these sources:', 'searchwp' ),
			'_engine_note' => __( 'This engine will search these sources:', 'searchwp' ),
			'_default_admin_engine_note' => wp_kses(
				__( 'This engine will be used for native WordPress <strong>and</strong> Admin searches, it will search these sources', 'searchwp' ),
				[ 'strong' => [], ]
			),
			'_edit_rules_for_source_engine' => sprintf(
				// Translators: 1st placeholder is the label of the Source, 2nd placeholder is the label of the Engine.
				__( 'Edit Rules for %1$s (%2$s Engine)', 'searchwp' ),
				'{{ searchwpPlaceholder1 }}', // This is a sequential token to be processed on the front end.
				'{{ searchwpPlaceholder2 }}'  // This is a sequential token to be processed on the front end.
			),
			'_edit_settings_engine' => sprintf(
				// Translators: placeholder is the label of the engine.
				__( 'Edit Settings (%s engine)', 'searchwp' ),
				'{{ searchwpPlaceholder1 }}' // This is a sequential token to be processed on the front end.
			),
			'_exclude_if' => wp_kses(
				__( '<strong>Exclude</strong> entries if:', 'searchwp' ),
				[ 'strong' => [], ]
			),
			'_export_note' => __( 'To export: choose what you want to export and then copy to clipboard', 'searchwp' ),
			'_http_loopback_note' => __( 'HTTP Loopback connections are not working on this server; this window must be left open for the initial index to build. Subsequent content edits will be reindexed automatically when made.', 'searchwp' ),
			'_import_note' => __( 'To import: paste settings export and click Import', 'searchwp' ),
			'_inactive_license_info' => wp_kses(
				sprintf(
					// Translators: 1st placeholder is an opening link, 2nd placeholder is closing, 3rd placeholder is an email link.
					__( '<p>Your license key can be retrieved or renewed within your %1$sAccount%2$s</p><p>If you are unable to activate your license due to network restrictions, please email %3$s first including your license key and then describing your issue.</p>', 'searchwp' ),
					'<a href="https://searchwp.com/account/" target="_blank">',
					'</a>',
					'<a href="mailto:support@searchwp.com">support@searchwp.com</a>'
				),
				[ 'p' => [], 'a' => [ 'href' => [], 'target' => [] ] ]
			),
			'_inactive_license_note' => __( 'Support and updates require an active license. Please activate your license to receive support.', 'searchwp' ),
			'_index_optimization_note' => __( 'Note: the index is automatically kept up to date and maintained for optimization', 'searchwp' ),
			'_index_outdated' => __( 'The index needs to be rebuilt.', 'searchwp' ),
			'_index_outdated_tooltip' => __( 'After certain engine configuration changes the index must be rebuilt', 'searchwp' ),
			'_indexer_blocked_note' => __( 'Indexer BLOCKED by HTTP Basic Authentication!', 'searchwp' ),
			'_indexer_paused' => __( 'The indexer is paused.', 'searchwp' ),
			'_indexer_paused_tooltip' => __( 'You can unpause the indexer from the Settings.', 'searchwp' ),
			'_invalid_default_engine_source_note' => __( 'Custom content Sources cannot be used in the Default engine', 'searchwp' ),
			'_keyword_stems_note' => __( 'Disregard keyword suffixes when searches are performed', 'searchwp' ),
			'_license_activation_problem' => __( 'There was a problem activating your license. Please ensure this server can communicate with searchwp.com and try again.', 'searchwp' ),
			'_license_deactivation_problem' => __( 'There was a problem deactivating your license. Please ensure this server can communicate with searchwp.com and try again.', 'searchwp' ),
			'_manage_engine_source_attributes' => sprintf(
				// Translators: 1st placeholder is the label of the Attribute, 2nd placeholder is the label of the Engine.
				__( 'Manage %1$s Attributes (%2$s Engine)', 'searchwp' ),
				'{{ searchwpPlaceholder1 }}', // This is a sequential token to be processed on the front end.
				'{{ searchwpPlaceholder2 }}'  // This is a sequential token to be processed on the front end.
			),
			'_manage_ignored_note' => __( 'The following queries have been ignored both when logging and displaying statistics.', 'searchwp' ),
			'_manage_ignored_note_none' => __( 'There are no ignored queries at this time.', 'searchwp' ),
			'_memory_limit_note' => sprintf(
				// Translators: first placeholder is the SearchWP recommended RAM amount, second placeholder is the WordPress memory limit, third placeholder is the RAM available to PHP.
				__( 'SearchWP recommends at least %1$s of memory. WordPress is set to use %2$s but there is %3$s available.', 'searchwp' ),
				'{{ searchwpPlaceholder1 }}', // This is a sequential token to be processed on the front end.
				'{{ searchwpPlaceholder2 }}', // This is a sequential token to be processed on the front end.
				'{{ searchwpPlaceholder3 }}'  // This is a sequential token to be processed on the front end.
			),
			'_needs_initial_save' => __( 'To enable SearchWP, please save your initial settings which builds the index', 'searchwp' ),
			'_no_attributes_note' => __( 'In order for results to be returned, you must add Attributes to be searched.', 'searchwp' ),
			'_no_rules_note' => __( 'There are no rules', 'searchwp' ),
			'_no_rules_for_note' => sprintf(
				// Translators: placeholder is the label of the Rule.
				__( 'There are currently no rules for %s.', 'searchwp' ),
				'{{ searchwpPlaceholder1 }}' // This is a sequential token to be processed on the front end.
			),
			'_no_sources_warning' => __( 'In order for this engine to return results, you must add at least one source', 'searchwp' ),
			'_no_stopwords_note' => __( 'There are currently no stopwords.', 'searchwp' ),
			'_no_suggested_stopwords_note' => __( 'There are no suggested stopwords to add at this time.', 'searchwp' ),
			'_no_synonyms_note' => __( 'There are currently no synonyms.', 'searchwp' ),
			'_only_show_if' => wp_kses(
				__( '<strong>Only show</strong> entries if:', 'searchwp' ),
				[ 'strong' => [], ]
			),
			'_stopwords_note' => wp_kses(
				sprintf(
					// Translators: 1st placeholder opens the link, 2nd placeholder closes it.
					__( 'Stopwords are <em>ignored</em> so as to improve relevancy and performance. %1$sMore info%2$s', 'searchwp' ),
					'<a href="https://searchwp.com/?p=424396#stopwords" target="_blank">',
					'</a>'
				),
				[ 'em' => [], 'a' => [ 'href' => [], 'target' => [] ] ]
			),
			'_suggested_stopwords_note' => __( 'The following terms may be too common to have a positive effect on searches, consider adding them as Stopwords.', 'searchwp' ),
			'_suggest_alternate_indexer_note' => __( 'The index does not appear to be building, you may want to enable the alternate indexer:', 'searchwp' ),
			'_synonyms_note' => wp_kses(
				sprintf(
					// Translators: 1st placeholder opens the link, 2nd placeholder closes it.
					__( 'Synonyms facilitate <em>replacement</em> of search terms. Use <code>*</code> wildcard for partial matching. %1$sMore info%2$s', 'searchwp' ),
					'<a href="https://searchwp.com/?p=424396#synonyms" target="_blank">',
					'</a>'
				),
				[ 'em' => [], 'a' => [ 'href' => [], 'target' => [] ], 'code' => [], ]
			),
			'_synonyms_replace_tooltip' => __( 'When enabled, original Search Term(s) will be removed', 'searchwp' ),
			'_synonyms_synonyms_tooltip' => __( 'Term(s) that are synonymous with the Search Term(s)', 'searchwp' ),
			'_synonyms_term_tooltip' => __( 'What visitors search for (separate different search terms by commas)', 'searchwp' ),
			'_rebuild_index_confirmation' => __( 'Are you sure you want to rebuild the index? This cannot be undone!', 'searchwp' ),
			'_save_source_attributes' => sprintf(
				// Translators: 1st placeholder is the name of a Source.
				__( 'Save %1$s Attributes', 'searchwp' ),
				'{{ sourceSingular }}'
			),
			'_system_information_note' => __( 'Please copy and paste this System Information when requested', 'searchwp' ),
			'_wake_indexer_note' => __( 'If the indexer appears to be stuck, first review the PHP error log to see if anything needs to be fixed before waking it up. The indexer can become stuck when customizations are not working as expected.', 'searchwp' ),
			'_welcome_blurb' => wp_kses(
				sprintf(
					// Translators: 1st placeholder opens the link, 2nd placeholder closes it.
					__( '<p>A default Engine has been generated as a starting point for you to customize. Once saved, SearchWP will then build its index and provide terrific search results automatically!</p><p>To find out more about customizing SearchWP, please %1$sreview the documentation%2$s.</p><p class="description">Thank you!</p>', 'searchwp' ),
					'<a href="https://searchwp.com/documentation/" target="_blank">',
					'</a>'
				),
				[ 'p' => [ 'class' => [] ], 'a' => [ 'href' => [], 'target' => [] ] ]
			),
			'_welcome_intro' => __( 'Thank you for activating SearchWP!', 'searchwp' ),
			'_welcome_migration_intro' => wp_kses(
				sprintf(
					// Translators: 1st placeholder opens the link, 2nd placeholder closes it.
					__( 'Your settings from SearchWP 3.x have been migrated. To delete legacy data from SearchWP 3.x please use %1$sthis Extension%2$s.', 'searchwp' ),
					'<a href="https://searchwp.com/extensions/legacy-data-removal/" target="_blank">',
					'</a>'
				),
				[ 'a' => [ 'href' => [], 'target' => [] ] ]
			),
			'_welcome_migration_success' => wp_kses(
				sprintf(
					// Translators: 1st placeholder opens the link, 2nd placeholder closes it.
					__( 'All data from SearchWP 3.x has been migrated! Legacy data can be removed with %1$sthis Extension%2$s.', 'searchwp' ),
					'<a href="https://searchwp.com/extensions/legacy-data-removal/" target="_blank">',
					'</a>'
				),
				[ 'a' => [ 'href' => [], 'target' => [] ] ]
			),
			'_welcome_migration_warning' => wp_kses(
				__( 'Statistics have <strong>NOT</strong> been migrated as it may take some time to do so.', 'searchwp' ),
				[ 'strong' => [], 'em' => [] ]
			),
			'_welcome_warning' => wp_kses(
				__( '<strong>Before SearchWP can take effect</strong> you will need to <em>review and save</em> your Engine(s)', 'searchwp' ),
				[ 'strong' => [], 'em' => [] ]
			),
			// Translators: this is the suffix for the Statistics tooltip.
			' Searches' => __( ' Searches', 'searchwp' ),
			'(This setting can be changed at any time on the Advanced tab of the SearchWP settings screen)' => __( '(This setting can be changed at any time on the Advanced tab of the SearchWP settings screen)', 'searchwp' ),
			'Actions & Settings' => __( 'Actions & Settings', 'searchwp' ),
			'Actions' => __( 'Actions', 'searchwp' ),
			'Activate' => __( 'Activate', 'searchwp' ),
			'Active' => __( 'Active', 'searchwp' ),
			'Add New' => __( 'Add New', 'searchwp' ),
			'Add Rule' => __( 'Add Rule', 'searchwp' ),
			'Add Stopword' => __( 'Add Stopword', 'searchwp' ),
			'Add' => __( 'Add', 'searchwp' ),
			'Add/Remove Attributes' => __( 'Add/Remove Attributes', 'searchwp' ),
			'Admin Engine' => __( 'Admin Engine', 'searchwp' ),
			'Advanced Settings' => __( 'Advanced Settings', 'searchwp' ),
			'AND' => __( 'AND', 'searchwp' ),
			'Applicable Attribute Relevance' => __( 'Applicable Attribute Relevance', 'searchwp' ),
			'Are you sure you want to delete this engine?' => __( 'Are you sure you want to delete this engine?', 'searchwp' ),
			'Are you sure? The existing background process will be destroyed and then restarted.' => __( 'Are you sure? The existing background process will be destroyed and then restarted.', 'searchwp' ),
			'Attributes' => __( 'Attributes', 'searchwp' ),
			'Attributes to search (slider sets relevance weight)' => __( 'Attributes to search (slider sets relevance weight)', 'searchwp' ),
			'Automatic "Did you mean?" corrections' => __( 'Automatic "Did you mean?" corrections', 'searchwp' ),
			'Automatically highlight search terms when possible' => __( 'Automatically highlight search terms when possible', 'searchwp' ),
			'Cancel' => __( 'Cancel', 'searchwp' ),
			'Clear Stopwords' => __( 'Clear Stopwords', 'searchwp' ),
			'Close' => __( 'Close', 'searchwp' ),
			'Collapse Sources' => __( 'Collapse Sources', 'searchwp' ),
			'Continue to queue (but do not apply) delta index updates' => __( 'Continue to queue (but do not apply) delta index updates', 'searchwp' ),
			'Copied!' => __( 'Copied!', 'searchwp' ),
			'Copy to clipboard' => __( 'Copy to clipboard', 'searchwp' ),
			'Deactivate' => __( 'Deactivate', 'searchwp' ),
			'Debugging enabled' => __( 'Debugging enabled', 'searchwp' ),
			'Delete Engine' => __( 'Delete Engine', 'searchwp' ),
			'Delete parsed document content when rebuilding Index' => __( 'Delete parsed document content when rebuilding Index', 'searchwp' ),
			'Delete' => __( 'Delete', 'searchwp' ),
			'Done' => __( 'Done', 'searchwp' ),
			'Edit Rules' => __( 'Edit Rules', 'searchwp' ),
			'Engine Label' => __( 'Engine Label', 'searchwp' ),
			'Engine Name' => __( 'Engine Name', 'searchwp' ),
			'Settings import complete' => __( 'Settings import complete', 'searchwp' ),
			'Exclude entries if:' => __( 'Exclude entries if:', 'searchwp' ),
			'Existing settings of the same type will be overwritten. Continue?' => __( 'Existing settings of the same type will be overwritten. Continue?', 'searchwp' ),
			'Expand Sources' => __( 'Expand Sources', 'searchwp' ),
			'Export' => __( 'Export', 'searchwp' ),
			'Find partial matches when search terms yield no results' => __( 'Find partial matches when search terms yield no results', 'searchwp' ),
			'Fix this' => __( 'Fix this', 'searchwp' ),
			'Get Help' => __( 'Get Help', 'searchwp' ),
			'Hide Announcements' => __( 'Hide Announcements', 'searchwp' ),
			'Hide plugin announcements and update details' => __( 'Hide plugin announcements and update details', 'searchwp' ),
			'Highlight terms in results' => __( 'Highlight terms in results', 'searchwp' ),
			'ID' => __( 'ID', 'searchwp' ),
			'If you are customizing Document Content, PDF Metadata, or image EXIF in any way, you may want to change that behavior and have SearchWP re-parse that content when rebuilding the index.' => __( 'If you are customizing Document Content, PDF Metadata, or image EXIF in any way, you may want to change that behavior and have SearchWP re-parse that content when rebuilding the index.', 'searchwp' ),
			'Import Settings' => __( 'Import Settings', 'searchwp' ),
			'Import' => __( 'Import', 'searchwp' ),
			'In order to optimize index rebuilds, SearchWP does not purge extracted Document Content, PDF Metadata, or image EXIF data by default.' => __( 'In order to optimize index rebuilds, SearchWP does not purge extracted Document Content, PDF Metadata, or image EXIF data by default.', 'searchwp' ),
			'Inactive' => __( 'Inactive', 'searchwp' ),
			'Index everything regardless of token length' => __( 'Index everything regardless of token length', 'searchwp' ),
			'Index expanded Shortcode output (at the time of indexing)' => __( 'Index expanded Shortcode output (at the time of indexing)', 'searchwp' ),
			'Index Status' => __( 'Index Status', 'searchwp' ),
			'Indexed' => __( 'Indexed', 'searchwp' ),
			'Indexer communication error. See console.' => __( 'Indexer communication error. See console.', 'searchwp' ),
			'Indexer Paused' => __( 'Indexer Paused', 'searchwp' ),
			'is' => __( 'is', 'searchwp' ),
			'Keyword Stems' => __( 'Keyword Stems', 'searchwp' ),
			'Last Activity' => __( 'Last Activity', 'searchwp' ),
			'Leaving this parsed content in place speeds up index rebuilds' => __( 'Leaving this parsed content in place speeds up index rebuilds', 'searchwp' ),
			'License' => __( 'License', 'searchwp' ),
			'Log information during indexing and searching for review' => __( 'Log information during indexing and searching for review', 'searchwp' ),
			'Manage Attributes' => __( 'Manage Attributes', 'searchwp' ),
			'Manage Ignored Queries' => __( 'Manage Ignored Queries', 'searchwp' ),
			'Manage Ignored' => __( 'Manage Ignored', 'searchwp' ),
			'Max' => __( 'Max', 'searchwp' ),
			'Migrate Statistics' => __( 'Migrate Statistics', 'searchwp' ),
			'Min' => __( 'Min', 'searchwp' ),
			'More Info' => __( 'More Info', 'searchwp' ),
			'Omitted Entries' => __( 'Omitted Entries', 'searchwp' ),
			'Omitted' => __( 'Omitted', 'searchwp' ),
			'Only show entries if:' => __( 'Only show entries if:', 'searchwp' ),
			'Options' => __( 'Options', 'searchwp' ),
			'Or choose from the following shortcuts' => __( 'Or choose from the following shortcuts', 'searchwp' ),
			'OR' => __( 'OR', 'searchwp' ),
			'Parse Shortcodes when indexing' => __( 'Parse Shortcodes when indexing', 'searchwp' ),
			'Partial matches (fuzzy when necessary)' => __( 'Partial matches (fuzzy when necessary)', 'searchwp' ),
			'Performing migration, please wait...' => __( 'Performing migration, please wait...', 'searchwp' ),
			'Prevalence' => __( 'Prevalence', 'searchwp' ),
			'Process less data per index pass (less resource intensive, but slower)' => __( 'Process less data per index pass (less resource intensive, but slower)', 'searchwp' ),
			'Query to ignore' => __( 'Query to ignore, use * as wildcard', 'searchwp' ),
			'Query' => __( 'Query', 'searchwp' ),
			'Queued updates will be processed immediately when the indexer is unpaused' => __( 'Queued updates will be processed immediately when the indexer is unpaused', 'searchwp' ),
			'Rebuild Index' => __( 'Rebuild Index', 'searchwp' ),
			'Reduced indexer aggressiveness' => __( 'Reduced indexer aggressiveness', 'searchwp' ),
			'Reintroduce' => __( 'Reintroduce', 'searchwp' ),
			'Remove' => __( 'Remove', 'searchwp' ),
			'Remove all data on uninstall' => __( 'Remove all data on uninstall', 'searchwp' ),
			'Remove all traces of SearchWP when it is deactivated and deleted from the Plugins page' => __( 'Remove all traces of SearchWP when it is deactivated and deleted from the Plugins page', 'searchwp' ),
			'Remove extracted Document Content, PDF Metadata, and image EXIF data and re-parse when rebuilding Index' => __( 'Remove extracted Document Content, PDF Metadata, and image EXIF data and re-parse when rebuilding Index', 'searchwp' ),
			'Remove minimum word length' => __( 'Remove minimum word length', 'searchwp' ),
			'Remove stored Document Content, PDF Metadata, and image EXIF when rebuilding index' => __( 'Remove stored Document Content, PDF Metadata, and image EXIF when rebuilding index', 'searchwp' ),
			'Replace' => __( 'Replace', 'searchwp' ),
			'Reset' => __( 'Reset', 'searchwp' ),
			'Resetting index...' => __( 'Resetting index...', 'searchwp' ),
			'Restore Defaults' => __( 'Restore Defaults', 'searchwp' ),
			'Rules' => __( 'Rules', 'searchwp' ),
			'Save Engines' => __( 'Save Engines', 'searchwp' ),
			'Save Settings' => __( 'Save Settings', 'searchwp' ),
			'Save' => __( 'Save', 'searchwp' ),
			'Saving settings FAILED! View console for more information' => __( 'Saving settings FAILED! View console for more information', 'searchwp' ),
			'Saving engine settings FAILED!' => __( 'Saving engine settings FAILED!', 'searchwp' ),
			'Saving Engines...' => __( 'Saving Engines...', 'searchwp' ),
			'Search Term(s)' => __( 'Search Term(s)', 'searchwp' ),
			'Search these sources:' => __( 'Search these sources:', 'searchwp' ),
			'Searches over the past 30 days' => __( 'Searches over the past 30 days', 'searchwp' ),
			'Searches' => __( 'Searches', 'searchwp' ),
			'SearchWP Advanced' => __( 'SearchWP Advanced', 'searchwp' ),
			'SearchWP Engines' => __( 'SearchWP Engines', 'searchwp' ),
			'SearchWP Settings' => __( 'SearchWP Settings', 'searchwp' ),
			'SearchWP Statistics' => __( 'SearchWP Statistics', 'searchwp' ),
			'SearchWP Support' => __( 'SearchWP Support', 'searchwp' ),
			'SearchWP was unable to index the following content due to an indexing failure. Reviewing server logs may expose the reason for failure.' => __( 'SearchWP was unable to index the following content due to an indexing failure. Reviewing server logs may expose the reason for failure.', 'searchwp' ),
			'Settings update FAILED' => __( 'Settings update FAILED', 'searchwp' ),
			'Settings' => __( 'Settings', 'searchwp' ),
			'Settings Transfer' => __( 'Settings Transfer', 'searchwp' ),
			'Sort Alphabetically' => __( 'Sort Alphabetically', 'searchwp' ),
			'Sort ASC' => __( 'Sort ASC', 'searchwp' ),
			'Sort DESC' => __( 'Sort DESC', 'searchwp' ),
			'Source' => __( 'Source', 'searchwp' ),
			'Sources & Settings' => __( 'Sources & Settings', 'searchwp' ),
			'Sources' => __( 'Sources', 'searchwp' ),
			'Statistic' => __( 'Statistic', 'searchwp' ),
			'Stopwords' => __( 'Stopwords', 'searchwp' ),
			'Suggested Stopwords' => __( 'Suggested Stopwords', 'searchwp' ),
			'Support "quoted/phrase searches"' => __( 'Support "quoted/phrase searches"', 'searchwp' ),
			'Support' => __( 'Support', 'searchwp' ),
			'Synonym(s)' => __( 'Synonym(s)', 'searchwp' ),
			'Synonyms' => __( 'Synonyms', 'searchwp' ),
			'System Information' => __( 'System Information', 'searchwp' ),
			'Term' => __( 'Term', 'searchwp' ), // Translators: prefaced by (translation of) "transfer weight".
			'There are no omitted entries at this time.' => __( 'There are no omitted entries at this time.', 'searchwp' ),
			'to entry ID' => __( 'to entry ID', 'searchwp' ), // Translators: prefaced by (translation of) "transfer weight".
			'Tokenize regex pattern matches' => __( 'Tokenize regex pattern matches', 'searchwp' ),
			'Total' => __( 'Total', 'searchwp' ),
			'Trim logs' => __( 'Trim logs', 'searchwp' ),
			'Type to search options...' => __( 'Type to search options...', 'searchwp' ),
			'Unignore' => __( 'Unignore', 'searchwp' ),
			'Update FAILED!' => __( 'Update FAILED!', 'searchwp' ),
			'Use for Admin searches' => __( 'Use for Admin searches', 'searchwp' ),
			'Use keyword stems' => __( 'Use keyword stems', 'searchwp' ),
			'Use the closest match for searches that yield no results and output a notice' => __( 'Use the closest match for searches with no results and output a notice (requires partial matches)', 'searchwp' ),
			'Value' => __( 'Value', 'searchwp' ),
			'View Suggestions' => __( 'View Suggestions', 'searchwp' ),
			'Wake Up Indexer' => __( 'Wake Up Indexer', 'searchwp' ),
			'Waking indexer FAILED. View console for more information.' => __( 'Waking indexer FAILED. View console for more information.', 'searchwp' ),
			'Welcome' => __( 'Welcome', 'searchwp' ),
			'When enabled, additional tokens will be generated from regex pattern matches' => __( 'When enabled, additional tokens will be generated from regex pattern matches', 'searchwp' ),
			'When search terms are wrapped in double quotes, results will be limited to those with exact matches' => __( 'When search terms are wrapped in double quotes, results will be limited to those with exact matches', 'searchwp' ),
			'Would you prefer that SearchWP remove all stored Document Content, PDF Metadata, and image EXIF data so it can be re-parsed during Index rebuilds?' => __( 'Would you prefer that SearchWP remove all stored Document Content, PDF Metadata, and image EXIF data so it can be re-parsed during Index rebuilds?', 'searchwp' ),
		];
	}
}
