export default ({ parentId }) => ({
    parentId,
    sortable: null,

    init() {
        this.sortable = new Sortable(this.$el, {
            group: 'nested',
            draggable: '[data-sortable-item]',
            handle: '[data-sortable-handle]',
            animation: 300,
            ghostClass: 'fi-sortable-ghost',
            dataIdAttr: 'data-sortable-item',
            onSort: (evt) => {
                const order = this.sortable.toArray();
                const actualParentId = this.parentId === 0 ? null : this.parentId;

                // Debug logging
                console.log('Reorder triggered:', {
                    parentId: this.parentId,
                    actualParentId: actualParentId,
                    order: order,
                    orderLength: order.length
                });

                this.$wire.reorder(order, actualParentId);
            },
        })
    },
})
