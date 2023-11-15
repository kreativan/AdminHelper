function dragAndDropSort() {

  $(document).ready(function () {

    /**
     *  Sortable
     *  for all elements wit #sortable cs id
     *  This is only ui sort, not doing any actions
     */
    let dragDropEl = $('.drag-and-drop');
    if (dragDropEl) {
      $(function () {
        dragDropEl.sortable({
          handle: '.handle',
          stop: function (event, ui) {
            // do something
          }
        });
        $('.drag-and-drop').disableSelection();
      });
    }

    let ivmSortable = $('#sortable');
    if (ivmSortable.length > 0) {
      $(function () {

        ivmSortable.sortable({
          handle: '.handle',
          stop: function (event, ui) {

            $('#sortable').css('opacity', '0.5');

            var id = $(ui.item).attr('data-id');
            var nextID = $(ui.item).next().attr('data-id');
            var prevID = $(ui.item).prev().attr('data-id');

            var ajaxURL = './';

            $.post(ajaxURL, {
              drag_drop_page_id: id,
              drag_drop_next_id: nextID,
              drag_drop_prev_id: prevID,
              action: "drag_drop_sort",
            }).done(function (data) {
              // console.log('Data Loaded: ' + data);
              $('#sortable').css('opacity', '1');
            });

          }
        });

        $('#sortable').disableSelection();

      });

    }

  });

}