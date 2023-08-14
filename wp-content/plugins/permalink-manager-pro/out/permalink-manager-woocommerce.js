(function(blocks, editor, element, components) {
	var el = element.createElement;
	var Button = components.Button;
	var ButtonGroup = components.ButtonGroup;
	var Modal = components.Modal;
	var withState = wp.compose.withState;

    var ModalContent = function({ attributes, setAttributes }) {
        return (
            el(
                'div', {
                    className: 'your-modal-class'
                },
                el('p', {}, 'Custom permalink:'),
                el('input', {
                    type: 'text',
                    value: attributes.customPermalink,
                    onChange: function(event) {
                        setAttributes({ customPermalink: event.target.value });
                    }
                }),
                el('p', {}, 'Native slug:'),
                el('input', {
                    type: 'text',
                    value: attributes.nativeSlug,
                    onChange: function(event) {
                        setAttributes({ nativeSlug: event.target.value });
                    }
                }),
                el(components.Button, {
                    className: 'is-content-block',
                    isDefault: true,
                    onClick: function() { alert('Save'); }
                }, 'Save'),
            )
        );
    }

    var ModalButton = withState({
        isOpen: false
    })(function({ isOpen, setState, attributes, setAttributes }) {
        return (
            el('div', {
                    className: 'block-editor-block-list__block wp-block is-content-block'
                },
                el(Button, {
                    className: 'is-content-block',
                    isDefault: true,
                    onClick: function() {
                        setState({
                            isOpen: true
                        });
                    },
                }, 'Permalink Manager'),
                isOpen && el(Modal, {
                    title: "Permalink Manager",
                    onRequestClose: function() {
                        setState({
                            isOpen: false
                        });
                    },
                }, el(ModalContent, {
                    attributes: attributes,
                    setAttributes: setAttributes
                }))
            )
        );
    });

    blocks.registerBlockType('permalink-manager/woocommerce', {
        title: 'Permalink Manager',
        category: 'common',
        attributes: {
            customPermalink: {
                type: 'string',
                default: 'FGH'
            },
            nativeSlug: {
                type: 'string',
                default: 'IJK'
            }
        },
        edit: function(props) {
            return el(ModalButton, {
                isOpen: props.isOpen,
                setState: props.setState,
                attributes: props.attributes,
                setAttributes: props.setAttributes
            });
        },
        save: function() {
            // Trigger the function after the product is saved
            function triggerFunctionAfterSave() {
                // Your custom function logic here
                console.log('Product saved!');
                alert('Product saved!');
            }

            // Add the event listener for 'save' event
            /*wp.data.dispatch('core/editor').addSaveHandler(function() {
                triggerFunctionAfterSave();
            });*/

            return null;
        },
    });
}(
	window.wp.blocks,
	window.wp.editor,
	window.wp.element,
	window.wp.components
));

/*(function (hooks, metaBoxes) {
    var addFilter = hooks.addFilter;
    var applyFilters = hooks.applyFilters;
    var addAction = hooks.addAction;

    // Hook into the wcSetPermalink action to update the permalink dynamically
    addAction('wcSetPermalink', 'myCustomAction', function (permalink, data) {
        // Get the updated permalink and modify it dynamically based on your requirements
        var updatedPermalink = applyFilters('myCustomPermalinkFilter', permalink, data);

        return updatedPermalink;
    });

    // Hook into the product title change event and update the permalink
    addFilter('woocommerce_product_title', 'myCustomProductTitleFilter', function (title, product) {
        // Update the permalink when the product title changes
        metaBoxes.updatePermalink(product);

        return title;
    });

    // Example: Modify the permalink based on the product title
    addFilter('myCustomPermalinkFilter', 'myCustomPermalinkFilter', function (permalink, data) {
        var productTitle = data.title;

        // Modify the permalink dynamically based on the product title
        var modifiedPermalink = permalink + '-' + productTitle.toLowerCase().replace(/\s+/g, '-');

        return modifiedPermalink;
    });
})(wp.hooks, wc.metaBoxes);*/