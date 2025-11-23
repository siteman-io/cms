// resources/js/components/menu.js
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
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsiLi4vLi4vLi4vanMvY29tcG9uZW50cy9tZW51LmpzIl0sCiAgInNvdXJjZXNDb250ZW50IjogWyJleHBvcnQgZGVmYXVsdCAoeyBwYXJlbnRJZCB9KSA9PiAoe1xuICAgIHBhcmVudElkLFxuICAgIHNvcnRhYmxlOiBudWxsLFxuXG4gICAgaW5pdCgpIHtcbiAgICAgICAgdGhpcy5zb3J0YWJsZSA9IG5ldyBTb3J0YWJsZSh0aGlzLiRlbCwge1xuICAgICAgICAgICAgZ3JvdXA6ICduZXN0ZWQnLFxuICAgICAgICAgICAgZHJhZ2dhYmxlOiAnW2RhdGEtc29ydGFibGUtaXRlbV0nLFxuICAgICAgICAgICAgaGFuZGxlOiAnW2RhdGEtc29ydGFibGUtaGFuZGxlXScsXG4gICAgICAgICAgICBhbmltYXRpb246IDMwMCxcbiAgICAgICAgICAgIGdob3N0Q2xhc3M6ICdmaS1zb3J0YWJsZS1naG9zdCcsXG4gICAgICAgICAgICBkYXRhSWRBdHRyOiAnZGF0YS1zb3J0YWJsZS1pdGVtJyxcbiAgICAgICAgICAgIG9uU29ydDogKCkgPT4ge1xuICAgICAgICAgICAgICAgIHRoaXMuJHdpcmUucmVvcmRlcihcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5zb3J0YWJsZS50b0FycmF5KCksXG4gICAgICAgICAgICAgICAgICAgIHRoaXMucGFyZW50SWQgPT09IDAgPyBudWxsIDogdGhpcy5wYXJlbnRJZCxcbiAgICAgICAgICAgICAgICApXG4gICAgICAgICAgICB9LFxuICAgICAgICB9KVxuICAgIH0sXG59KVxuIl0sCiAgIm1hcHBpbmdzIjogIjtBQUFBLElBQU8sZUFBUSxDQUFDLEVBQUUsU0FBUyxPQUFPO0FBQUEsRUFDOUI7QUFBQSxFQUNBLFVBQVU7QUFBQSxFQUVWLE9BQU87QUFDSCxTQUFLLFdBQVcsSUFBSSxTQUFTLEtBQUssS0FBSztBQUFBLE1BQ25DLE9BQU87QUFBQSxNQUNQLFdBQVc7QUFBQSxNQUNYLFFBQVE7QUFBQSxNQUNSLFdBQVc7QUFBQSxNQUNYLFlBQVk7QUFBQSxNQUNaLFlBQVk7QUFBQSxNQUNaLFFBQVEsTUFBTTtBQUNWLGFBQUssTUFBTTtBQUFBLFVBQ1AsS0FBSyxTQUFTLFFBQVE7QUFBQSxVQUN0QixLQUFLLGFBQWEsSUFBSSxPQUFPLEtBQUs7QUFBQSxRQUN0QztBQUFBLE1BQ0o7QUFBQSxJQUNKLENBQUM7QUFBQSxFQUNMO0FBQ0o7IiwKICAibmFtZXMiOiBbXQp9Cg==
