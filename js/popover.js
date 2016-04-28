$(function(){
    $("#madeShot").popover({
        html : true, 
        content: function() {
          return $('#assistForPopoverContent').html();
        },
        title: function() {
          return $('#assistForPopoverTitle').html();
        },
    });

});