// resources/js/menu.js
var menu_default = ({ parentId }) => ({
  parentId,
  sortable: null,
  init() {
    this.sortable = new Sortable(this.$el, {
      group: "nested",
      draggable: "[data-sortable-item]",
      handle: "[data-sortable-handle]",
      animation: 300,
      ghostClass: "fi-sortable-ghost",
      dataIdAttr: "data-sortable-item",
      onSort: () => {
        this.$wire.reorder(
          this.sortable.toArray(),
          this.parentId === 0 ? null : this.parentId
        );
      }
    });
  }
});
export {
  menu_default as default
};
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsiLi4vLi4vanMvbWVudS5qcyJdLAogICJzb3VyY2VzQ29udGVudCI6IFsiZXhwb3J0IGRlZmF1bHQgKHsgcGFyZW50SWQgfSkgPT4gKHtcbiAgICBwYXJlbnRJZCxcbiAgICBzb3J0YWJsZTogbnVsbCxcblxuICAgIGluaXQoKSB7XG4gICAgICAgIHRoaXMuc29ydGFibGUgPSBuZXcgU29ydGFibGUodGhpcy4kZWwsIHtcbiAgICAgICAgICAgIGdyb3VwOiAnbmVzdGVkJyxcbiAgICAgICAgICAgIGRyYWdnYWJsZTogJ1tkYXRhLXNvcnRhYmxlLWl0ZW1dJyxcbiAgICAgICAgICAgIGhhbmRsZTogJ1tkYXRhLXNvcnRhYmxlLWhhbmRsZV0nLFxuICAgICAgICAgICAgYW5pbWF0aW9uOiAzMDAsXG4gICAgICAgICAgICBnaG9zdENsYXNzOiAnZmktc29ydGFibGUtZ2hvc3QnLFxuICAgICAgICAgICAgZGF0YUlkQXR0cjogJ2RhdGEtc29ydGFibGUtaXRlbScsXG4gICAgICAgICAgICBvblNvcnQ6ICgpID0+IHtcbiAgICAgICAgICAgICAgICB0aGlzLiR3aXJlLnJlb3JkZXIoXG4gICAgICAgICAgICAgICAgICAgIHRoaXMuc29ydGFibGUudG9BcnJheSgpLFxuICAgICAgICAgICAgICAgICAgICB0aGlzLnBhcmVudElkID09PSAwID8gbnVsbCA6IHRoaXMucGFyZW50SWQsXG4gICAgICAgICAgICAgICAgKVxuICAgICAgICAgICAgfSxcbiAgICAgICAgfSlcbiAgICB9LFxufSlcbiJdLAogICJtYXBwaW5ncyI6ICI7QUFBQSxJQUFPLGVBQVEsQ0FBQyxFQUFFLFNBQVMsT0FBTztBQUFBLEVBQzlCO0FBQUEsRUFDQSxVQUFVO0FBQUEsRUFFVixPQUFPO0FBQ0gsU0FBSyxXQUFXLElBQUksU0FBUyxLQUFLLEtBQUs7QUFBQSxNQUNuQyxPQUFPO0FBQUEsTUFDUCxXQUFXO0FBQUEsTUFDWCxRQUFRO0FBQUEsTUFDUixXQUFXO0FBQUEsTUFDWCxZQUFZO0FBQUEsTUFDWixZQUFZO0FBQUEsTUFDWixRQUFRLE1BQU07QUFDVixhQUFLLE1BQU07QUFBQSxVQUNQLEtBQUssU0FBUyxRQUFRO0FBQUEsVUFDdEIsS0FBSyxhQUFhLElBQUksT0FBTyxLQUFLO0FBQUEsUUFDdEM7QUFBQSxNQUNKO0FBQUEsSUFDSixDQUFDO0FBQUEsRUFDTDtBQUNKOyIsCiAgIm5hbWVzIjogW10KfQo=
