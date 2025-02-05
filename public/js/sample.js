$(document).ready(function() {
  $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
  $('#searchForm').on('submit', function(e) {
    
      e.preventDefault(); // フォームのデフォルトの送信を防止

      $.ajax({
          url: $(this).attr('action'),
          method: $(this).attr('method'),
          data: $(this).serialize(),
          success: function(response) {
              // 非同期で取得した検索結果で商品情報を更新
              $('.products').html($(response).find('.products').html());
            },
          error: function(xhr) {
              console.error('検索結果の取得中にエラーが発生しました');
          }
      });
  });

 
  //この下から削除非同期処理
  console.log('読み込みOK');

  $(document).on('click', '.deleteTarget', function(e) {
      e.preventDefault(); // フォームのデフォルトの送信を防ぐ
      console.log('削除処理OK');
      var deleteConfirm = confirm('削除してよろしいでしょうか？');
      if (deleteConfirm) {
          var button = $(this);
          var productID = button.data('product-id'); // フォームからデータ属性を取得
          $.ajax({
              url: 'products/' + productID,
              type: 'POST',
              data: {
                  '_method': 'DELETE', // DELETEリクエスト
                  '_token': $('meta[name="csrf-token"]').attr('content') // CSRFトークン
              }
          })
          .done(function() {
              console.log('通信成功');
              button.parents('tr').remove(); // 通信が成功した場合、フォームの親要素の <tr> を削除
          })
          .fail(function() {
              alert('エラー');
          });
      }
  });
});

