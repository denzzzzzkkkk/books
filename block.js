(function (blocks, element, editor) {
    var el = element.createElement;
    var registerBlockType = blocks.registerBlockType;
    var RichText = editor.RichText;

    registerBlockType('custom-books-plugin/books-block', {
        title: 'Books Block',
        icon: 'book-alt',
        category: 'common',
        attributes: {
            content: {
                type: 'array',
                source: 'children',
                selector: 'p',
            },
        },
        edit: function (props) {
            var content = props.attributes.content;
            var onChangeContent = function (newContent) {
                props.setAttributes({ content: newContent });
            };

            return el(RichText, {
                tagName: 'p',
                className: props.className,
                value: content,
                onChange: onChangeContent,
            });
        },
        save: function (props) {
            return el(RichText.Content, {
                tagName: 'p',
                value: props.attributes.content,
            });
        },
    });
})(
    window.wp.blocks,
    window.wp.element,
    window.wp.editor
);
