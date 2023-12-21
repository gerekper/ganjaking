<?php
/**
 * Source Code
 *
 * @package Happy_Addons
 */

namespace Happy_Addons_Pro\Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;

defined('ABSPATH') || die();

class Source_Code extends Base {

	/**
	 * Get widget title.
	 *
	 * @return string Widget title.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function get_title() {
		return __('Source Code', 'happy-addons-pro');
	}

	/**
	 * Get widget icon.
	 *
	 * @return string Widget icon.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function get_icon() {
		return 'hm hm-code-browser';
	}

	public function get_keywords() {
		return ['source-code', 'source', 'code'];
	}

	public function lng_type() {
		return [
			'markup' => __('HTML Markup', 'happy-addons-pro'),
			'css' => __('CSS', 'happy-addons-pro'),
			'clike' => __('Clike', 'happy-addons-pro'),
			'javascript' => __('JavaScript', 'happy-addons-pro'),
			'abap' => __('ABAP', 'happy-addons-pro'),
			'abnf' => __('Augmented Backus–Naur form', 'happy-addons-pro'),
			'actionscript' => __('ActionScript', 'happy-addons-pro'),
			'ada' => __('Ada', 'happy-addons-pro'),
			'apacheconf' => __('Apache Configuration', 'happy-addons-pro'),
			'apl' => __('APL', 'happy-addons-pro'),
			'applescript' => __('AppleScript', 'happy-addons-pro'),
			'arduino' => __('Arduino', 'happy-addons-pro'),
			'arff' => __('ARFF', 'happy-addons-pro'),
			'asciidoc' => __('AsciiDoc', 'happy-addons-pro'),
			'asm6502' => __('6502 Assembly', 'happy-addons-pro'),
			'aspnet' => __('ASP.NET (C#)', 'happy-addons-pro'),
			'autohotkey' => __('AutoHotkey', 'happy-addons-pro'),
			'autoit' => __('Autoit', 'happy-addons-pro'),
			'bash' => __('Bash', 'happy-addons-pro'),
			'basic' => __('BASIC', 'happy-addons-pro'),
			'batch' => __('Batch', 'happy-addons-pro'),
			'bison' => __('Bison', 'happy-addons-pro'),
			'bnf' => __('Bnf', 'happy-addons-pro'),
			'brainfuck' => __('Brainfuck', 'happy-addons-pro'),
			'bro' => __('Bro', 'happy-addons-pro'),
			'c' => __('C', 'happy-addons-pro'),
			'csharp' => __('Csharp', 'happy-addons-pro'),
			'cpp' => __('Cpp', 'happy-addons-pro'),
			'cil' => __('Cil', 'happy-addons-pro'),
			'coffeescript' => __('Coffeescript', 'happy-addons-pro'),
			'cmake' => __('Cmake', 'happy-addons-pro'),
			'clojure' => __('Clojure', 'happy-addons-pro'),
			'crystal' => __('Crystal', 'happy-addons-pro'),
			'csp' => __('Csp', 'happy-addons-pro'),
			'css-extras' => __('Css-extras', 'happy-addons-pro'),
			'd' => __('D', 'happy-addons-pro'),
			'dart' => __('Dart', 'happy-addons-pro'),
			'diff' => __('Diff', 'happy-addons-pro'),
			'django' => __('Django', 'happy-addons-pro'),
			'dns-zone-file' => __('Dns-zone-file', 'happy-addons-pro'),
			'docker' => __('Docker', 'happy-addons-pro'),
			'ebnf' => __('Ebnf', 'happy-addons-pro'),
			'eiffel' => __('Eiffel', 'happy-addons-pro'),
			'ejs' => __('Ejs', 'happy-addons-pro'),
			'elixir' => __('Elixir', 'happy-addons-pro'),
			'elm' => __('Elm', 'happy-addons-pro'),
			'erb' => __('Erb', 'happy-addons-pro'),
			'erlang' => __('Erlang', 'happy-addons-pro'),
			'fsharp' => __('Fsharp', 'happy-addons-pro'),
			'firestore-security-rules' => __('Firestore-security-rules', 'happy-addons-pro'),
			'flow' => __('Flow', 'happy-addons-pro'),
			'fortran' => __('Fortran', 'happy-addons-pro'),
			'gcode' => __('Gcode', 'happy-addons-pro'),
			'gdscript' => __('Gdscript', 'happy-addons-pro'),
			'gedcom' => __('Gedcom', 'happy-addons-pro'),
			'gherkin' => __('Gherkin', 'happy-addons-pro'),
			'git' => __('Git', 'happy-addons-pro'),
			'glsl' => __('Glsl', 'happy-addons-pro'),
			'gml' => __('Gml', 'happy-addons-pro'),
			'go' => __('Go', 'happy-addons-pro'),
			'graphql' => __('Graphql', 'happy-addons-pro'),
			'groovy' => __('Groovy', 'happy-addons-pro'),
			'haml' => __('Haml', 'happy-addons-pro'),
			'handlebars' => __('Handlebars', 'happy-addons-pro'),
			'haskell' => __('Haskell', 'happy-addons-pro'),
			'haxe' => __('Haxe', 'happy-addons-pro'),
			'hcl' => __('Hcl', 'happy-addons-pro'),
			'http' => __('Http', 'happy-addons-pro'),
			'hpkp' => __('Hpkp', 'happy-addons-pro'),
			'hsts' => __('Hsts', 'happy-addons-pro'),
			'ichigojam' => __('Ichigojam', 'happy-addons-pro'),
			'icon' => __('Icon', 'happy-addons-pro'),
			'inform7' => __('Inform7', 'happy-addons-pro'),
			'ini' => __('Ini', 'happy-addons-pro'),
			'io' => __('Io', 'happy-addons-pro'),
			'j' => __('J', 'happy-addons-pro'),
			'java' => __('Java', 'happy-addons-pro'),
			'javadoc' => __('Javadoc', 'happy-addons-pro'),
			'javadoclike' => __('Javadoclike', 'happy-addons-pro'),
			'javastacktrace' => __('Javastacktrace', 'happy-addons-pro'),
			'jolie' => __('Jolie', 'happy-addons-pro'),
			'jq' => __('Jq', 'happy-addons-pro'),
			'jsdoc' => __('Jsdoc', 'happy-addons-pro'),
			'js-extras' => __('Js-extras', 'happy-addons-pro'),
			'js-templates' => __('Js-templates', 'happy-addons-pro'),
			'json' => __('Json', 'happy-addons-pro'),
			'jsonp' => __('Jsonp', 'happy-addons-pro'),
			'json5' => __('Json5', 'happy-addons-pro'),
			'julia' => __('Julia', 'happy-addons-pro'),
			'keyman' => __('Keyman', 'happy-addons-pro'),
			'kotlin' => __('Kotlin', 'happy-addons-pro'),
			'latex' => __('Latex', 'happy-addons-pro'),
			'less' => __('Less', 'happy-addons-pro'),
			'lilypond' => __('Lilypond', 'happy-addons-pro'),
			'liquid' => __('Liquid', 'happy-addons-pro'),
			'lisp' => __('Lisp', 'happy-addons-pro'),
			'livescript' => __('Livescript', 'happy-addons-pro'),
			'lolcode' => __('Lolcode', 'happy-addons-pro'),
			'lua' => __('Lua', 'happy-addons-pro'),
			'makefile' => __('Makefile', 'happy-addons-pro'),
			'markdown' => __('Markdown', 'happy-addons-pro'),
			'markup-templating' => __('Markup-templating', 'happy-addons-pro'),
			'matlab' => __('Matlab', 'happy-addons-pro'),
			'mel' => __('Mel', 'happy-addons-pro'),
			'mizar' => __('Mizar', 'happy-addons-pro'),
			'monkey' => __('Monkey', 'happy-addons-pro'),
			'n1ql' => __('N1ql', 'happy-addons-pro'),
			'n4js' => __('N4js', 'happy-addons-pro'),
			'nand2tetris-hdl' => __('Nand2tetris-hdl', 'happy-addons-pro'),
			'nasm' => __('Nasm', 'happy-addons-pro'),
			'nginx' => __('Nginx', 'happy-addons-pro'),
			'nim' => __('Nim', 'happy-addons-pro'),
			'nix' => __('Nix', 'happy-addons-pro'),
			'nsis' => __('Nsis', 'happy-addons-pro'),
			'objectivec' => __('Objectivec', 'happy-addons-pro'),
			'ocaml' => __('Ocaml', 'happy-addons-pro'),
			'opencl' => __('Opencl', 'happy-addons-pro'),
			'oz' => __('Oz', 'happy-addons-pro'),
			'parigp' => __('Parigp', 'happy-addons-pro'),
			'parser' => __('Parser', 'happy-addons-pro'),
			'pascal' => __('Pascal', 'happy-addons-pro'),
			'pascaligo' => __('Pascaligo', 'happy-addons-pro'),
			'pcaxis' => __('Pcaxis', 'happy-addons-pro'),
			'perl' => __('Perl', 'happy-addons-pro'),
			'php' => __('Php', 'happy-addons-pro'),
			'phpdoc' => __('Phpdoc', 'happy-addons-pro'),
			'php-extras' => __('Php-extras', 'happy-addons-pro'),
			'plsql' => __('Plsql', 'happy-addons-pro'),
			'powershell' => __('Powershell', 'happy-addons-pro'),
			'processing' => __('Processing', 'happy-addons-pro'),
			'prolog' => __('Prolog', 'happy-addons-pro'),
			'properties' => __('Properties', 'happy-addons-pro'),
			'protobuf' => __('Protobuf', 'happy-addons-pro'),
			'pug' => __('Pug', 'happy-addons-pro'),
			'puppet' => __('Puppet', 'happy-addons-pro'),
			'pure' => __('Pure', 'happy-addons-pro'),
			'python' => __('Python', 'happy-addons-pro'),
			'q' => __('Q', 'happy-addons-pro'),
			'qore' => __('Qore', 'happy-addons-pro'),
			'r' => __('R', 'happy-addons-pro'),
			'jsx' => __('Jsx', 'happy-addons-pro'),
			'tsx' => __('Tsx', 'happy-addons-pro'),
			'renpy' => __('Renpy', 'happy-addons-pro'),
			'reason' => __('Reason', 'happy-addons-pro'),
			'regex' => __('Regex', 'happy-addons-pro'),
			'rest' => __('Rest', 'happy-addons-pro'),
			'rip' => __('Rip', 'happy-addons-pro'),
			'roboconf' => __('Roboconf', 'happy-addons-pro'),
			'ruby' => __('Ruby', 'happy-addons-pro'),
			'rust' => __('Rust', 'happy-addons-pro'),
			'sas' => __('Sas', 'happy-addons-pro'),
			'sass' => __('Sass', 'happy-addons-pro'),
			'scss' => __('Scss', 'happy-addons-pro'),
			'scala' => __('Scala', 'happy-addons-pro'),
			'scheme' => __('Scheme', 'happy-addons-pro'),
			'shell-session' => __('Shell-session', 'happy-addons-pro'),
			'smalltalk' => __('Smalltalk', 'happy-addons-pro'),
			'smarty' => __('Smarty', 'happy-addons-pro'),
			'soy' => __('Soy', 'happy-addons-pro'),
			'splunk-spl' => __('Splunk-spl', 'happy-addons-pro'),
			'sql' => __('Sql', 'happy-addons-pro'),
			'stylus' => __('Stylus', 'happy-addons-pro'),
			'swift' => __('Swift', 'happy-addons-pro'),
			'tap' => __('Tap', 'happy-addons-pro'),
			'tcl' => __('Tcl', 'happy-addons-pro'),
			'textile' => __('Textile', 'happy-addons-pro'),
			'toml' => __('Toml', 'happy-addons-pro'),
			'tt2' => __('Tt2', 'happy-addons-pro'),
			'turtle' => __('Turtle', 'happy-addons-pro'),
			'twig' => __('Twig', 'happy-addons-pro'),
			'typescript' => __('Typescript', 'happy-addons-pro'),
			't4-cs' => __('T4-cs', 'happy-addons-pro'),
			't4-vb' => __('T4-vb', 'happy-addons-pro'),
			't4-templating' => __('T4-templating', 'happy-addons-pro'),
			'vala' => __('Vala', 'happy-addons-pro'),
			'vbnet' => __('Vbnet', 'happy-addons-pro'),
			'velocity' => __('Velocity', 'happy-addons-pro'),
			'verilog' => __('Verilog', 'happy-addons-pro'),
			'vhdl' => __('Vhdl', 'happy-addons-pro'),
			'vim' => __('Vim', 'happy-addons-pro'),
			'visual-basic' => __('Visual-basic', 'happy-addons-pro'),
			'wasm' => __('Wasm', 'happy-addons-pro'),
			'wiki' => __('Wiki', 'happy-addons-pro'),
			'xeora' => __('Xeora', 'happy-addons-pro'),
			'xojo' => __('Xojo', 'happy-addons-pro'),
			'xquery' => __('Xquery', 'happy-addons-pro'),
			'yaml' => __('Yaml', 'happy-addons-pro'),
		];
	}

	/**
     * Register widget content controls
     */
	protected function register_content_controls() {
		$this->__source_code_content_controls();
		$this->__custom_color_content_controls();
	}

	protected function __source_code_content_controls() {

		$this->start_controls_section(
			'_section_source_code',
			[
				'label' => __('Source Code', 'happy-addons-pro'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'lng_type',
			[
				'label' => __('Language Type', 'happy-addons-pro'),
				'label_block' => true,
				'type' => Controls_Manager::SELECT,
				'default' => 'markup',
				'options' => $this->lng_type(),
			]
		);

		$this->add_control(
			'theme',
			[
				'label' => __('Theme', 'happy-addons-pro'),
				'label_block' => true,
				'type' => Controls_Manager::SELECT,
				'default' => 'prism',
				'options' => [
					'prism' => __('Default', 'happy-addons-pro'),
					'prism-coy' => __('Coy', 'happy-addons-pro'),
					'prism-dark' => __('Dark', 'happy-addons-pro'),
					'prism-funky' => __('Funky', 'happy-addons-pro'),
					'prism-okaidia' => __('Okaidia', 'happy-addons-pro'),
					'prism-solarizedlight' => __('Solarized light', 'happy-addons-pro'),
					'prism-tomorrow' => __('Tomorrow', 'happy-addons-pro'),
					'prism-twilight' => __('Twilight', 'happy-addons-pro'),
					'custom' => __('Custom Color', 'happy-addons-pro'),
				],
                'style_transfer' => true,
			]
		);

		$this->add_control(
			'source_code',
			[
				'label' => __('Source Code', 'happy-addons-pro'),
				'type' => Controls_Manager::CODE,
				'rows' => 20,
				'default' => '<p class="random-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit.</p>',
				'placeholder' => __('Source Code....', 'happy-addons-pro'),
				'condition' => [
					'lng_type!' => '',
				],
			]
		);
		$this->add_control(
			'copy_btn_text_show',
			[
				'label' => __('Copy Button Text Show?', 'happy-addons-pro'),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'yes',
                'style_transfer' => true,
			]
		);
		$this->add_control(
			'copy_btn_text',
			[
				'label' => __('Copy Button Text', 'happy-addons-pro'),
				'type' => Controls_Manager::TEXT,
				'rows' => 10,
				'default' => __('Copy to clipboard', 'happy-addons-pro'),
				'placeholder' => __('Copy Button Text', 'happy-addons-pro'),
				'condition' => [
					'copy_btn_text_show' => 'yes',
				],
			]
		);
		$this->add_control(
			'after_copy_btn_text',
			[
				'label' => __('After Copy Button Text', 'happy-addons-pro'),
				'type' => Controls_Manager::TEXT,
				'rows' => 10,
				'default' => __('Copied', 'happy-addons-pro'),
				'placeholder' => __('Copied', 'happy-addons-pro'),
				'condition' => [
					'copy_btn_text_show' => 'yes',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function __custom_color_content_controls() {

		$this->start_controls_section(
			'_section_source_code_custom_color',
			[
				'label' => __('Custom Color', 'happy-addons-pro'),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'theme' => 'custom',
				],
			]
		);
		$this->add_control(
			'custom_background',
			[
				'label' => __( 'Background Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .custom :not(pre) > code[class*="language-"],{{WRAPPER}} .custom pre[class*="language-"]' => 'background: {{VALUE}}',
				],
				'condition' => [
					'theme' => 'custom',
				],
			]
		);
		$this->add_control(
			'custom_text_color',
			[
				'label' => __( 'Text Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .custom code[class*="language-"],{{WRAPPER}} .custom pre[class*="language-"]' => 'color: {{VALUE}}',
				],
				'condition' => [
					'theme' => 'custom',
				],
			]
		);
		$this->add_control(
			'custom_text_shadow_color',
			[
				'label' => __( 'Text shadow Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .custom code[class*="language-"],{{WRAPPER}} .custom pre[class*="language-"]' => 'text-shadow: 0 1px {{VALUE}}',
				],
				'condition' => [
					'theme' => 'custom',
				],
			]
		);
		$this->add_control(
			'custom_slate_gray',
			[
				'label' => __( 'Slate Gray Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .custom .token.comment,{{WRAPPER}} .custom .token.prolog,{{WRAPPER}} .custom .token.doctype,{{WRAPPER}} .custom .token.cdata' => 'color: {{VALUE}}',
				],
				'condition' => [
					'theme' => 'custom',
				],
			]
		);
		$this->add_control(
			'custom_dusty_gray',
			[
				'label' => __( 'Dusty Gray Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .custom .token.punctuation' => 'color: {{VALUE}}',
				],
				'condition' => [
					'theme' => 'custom',
				],
			]
		);
		$this->add_control(
			'custom_fresh_eggplant',
			[
				'label' => __( 'Fresh Eggplant Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .custom .token.property,{{WRAPPER}} .custom .token.tag,{{WRAPPER}} .custom .token.boolean,{{WRAPPER}} .custom .token.number,{{WRAPPER}} .custom .token.constant,{{WRAPPER}} .custom .token.symbol,{{WRAPPER}} .custom .token.deleted' => 'color: {{VALUE}}',
				],
				'condition' => [
					'theme' => 'custom',
				],
			]
		);
		$this->add_control(
			'custom_limeade',
			[
				'label' => __( 'Limeade Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .custom .token.selector,{{WRAPPER}} .custom .token.attr-name,{{WRAPPER}} .custom .token.string,{{WRAPPER}} .custom .token.char,{{WRAPPER}} .custom .token.builtin,{{WRAPPER}} .custom .token.inserted' => 'color: {{VALUE}}',
				],
				'condition' => [
					'theme' => 'custom',
				],
			]
		);
		$this->add_control(
			'custom_sepia_skin',
			[
				'label' => __( 'Sepia Skin Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .custom .token.operator,{{WRAPPER}} .custom .token.entity,{{WRAPPER}} .custom .token.url,{{WRAPPER}} .custom .language-css .token.string,{{WRAPPER}} .custom .style .token.string' => 'color: {{VALUE}}',
				],
				'condition' => [
					'theme' => 'custom',
				],
			]
		);
		$this->add_control(
			'custom_xanadu',
			[
				'label' => __( 'Xanadu Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .custom .token.operator,{{WRAPPER}} .custom .token.entity,{{WRAPPER}} .custom .token.url,{{WRAPPER}} .custom .language-css .token.string,{{WRAPPER}} .custom .style .token.string' => 'background: {{VALUE}}',
				],
				'condition' => [
					'theme' => 'custom',
				],
			]
		);
		$this->add_control(
			'custom_deep_cerulean',
			[
				'label' => __( 'Deep Cerulean Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .custom .token.atrule,{{WRAPPER}} .custom .token.attr-value,{{WRAPPER}} .custom .token.keyword' => 'color: {{VALUE}}',
				],
				'condition' => [
					'theme' => 'custom',
				],
			]
		);
		$this->add_control(
			'custom_cabaret',
			[
				'label' => __( 'Cabaret Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .custom .token.function,{{WRAPPER}} .custom .token.class-name' => 'color: {{VALUE}}',
				],
				'condition' => [
					'theme' => 'custom',
				],
			]
		);
		$this->add_control(
			'custom_tangerine',
			[
				'label' => __( 'Tangerine Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .custom .token.regex,{{WRAPPER}} .custom .token.important,{{WRAPPER}} .custom .token.variable' => 'color: {{VALUE}}',
				],
				'condition' => [
					'theme' => 'custom',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
     * Register widget style controls
     */
	protected function register_style_controls() {

		$this->start_controls_section(
			'_section_source_code_style',
			[
				'label' => __('Style', 'happy-addons-pro'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'source_code_box_height',
			[
				'label' => __('Height', 'happy-addons-pro'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-source-code pre' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'box_border',
				'label' => __('Box Border', 'happy-addons-pro'),
				'selector' => '{{WRAPPER}}  .ha-source-code pre[class*="language-"]',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'box_border_radius',
			[
				'label' => __('Border Radius', 'happy-addons-pro'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .ha-source-code pre[class*="language-"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'source_code_box_padding',
			[
				'label' => __('Padding', 'happy-addons-pro'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .ha-source-code pre' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$this->add_responsive_control(
			'source_code_box_margin',
			[
				'label' => __('Margin', 'happy-addons-pro'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .ha-source-code pre' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$this->add_control(
			'copy_btn_color',
			[
				'label' => __( 'Copy Button Text Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-copy-code-button' => 'color: {{VALUE}}',
				],
				'separator' => 'before',
				'condition' => [
					'copy_btn_text_show' => 'yes',
				],
			]
		);

		$this->add_control(
			'copy_btn_bg',
			[
				'label' => __( 'Copy Button Background', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-copy-code-button' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'copy_btn_text_show' => 'yes',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$source_code = $settings['source_code'];
		$theme = !empty($settings['theme']) ? $settings['theme'] : 'prism';
		$this->add_render_attribute('ha-code-wrap', 'class', 'ha-source-code');
		$this->add_render_attribute('ha-code-wrap', 'class', $theme);
		$this->add_render_attribute('ha-code-wrap', 'data-lng-type', $settings['lng_type']);
		if ('yes' == $settings['copy_btn_text_show'] && $settings['after_copy_btn_text']) {
			$this->add_render_attribute('ha-code-wrap', 'data-after-copy', $settings['after_copy_btn_text']);
		}
		$this->add_render_attribute('ha-code', 'class', 'language-' . $settings['lng_type']);
		?>
		<?php if (!empty($source_code)): ?>
			<div <?php $this->print_render_attribute_string('ha-code-wrap'); ?>>
			<pre>
			<?php if ('yes' == $settings['copy_btn_text_show'] && $settings['copy_btn_text']): ?>
				<button class="ha-copy-code-button"><?php echo esc_html($settings['copy_btn_text']) ?></button>
			<?php endif; ?>
				<code <?php $this->print_render_attribute_string('ha-code'); ?>>
					<?php echo esc_html($source_code); ?>
				</code>
			</pre>
			</div>
		<?php endif; ?>
		<?php

	}

	public function content_template() {
		?>
		<#
		var source_code = settings.source_code;
		view.addRenderAttribute( 'ha-code-wrap', 'class', 'ha-source-code');
		view.addRenderAttribute( 'ha-code-wrap', 'class', settings.theme);
		view.addRenderAttribute( 'ha-code-wrap', 'data-lng-type', settings.lng_type);
		if('yes' == settings.copy_btn_text_show && settings.after_copy_btn_text){
		view.addRenderAttribute( 'ha-code-wrap', 'data-after-copy', settings.after_copy_btn_text);
		}
		view.addRenderAttribute( 'ha-code', 'class', 'language-'+settings.lng_type);

		#>
		<# if( source_code ){ #>
		<div {{{ view.getRenderAttributeString( 'ha-code-wrap' ) }}}>
		<pre>
			<# if( 'yes' == settings.copy_btn_text_show && settings.copy_btn_text ){ #>
				<button class="ha-copy-code-button">{{{settings.copy_btn_text}}}</button>
			<# } #>
				<code {{{ view.getRenderAttributeString( 'ha-code' ) }}}>{{ source_code }}</code>
			</pre>
		</div>
		<# } #>

		<?php
	}

}
