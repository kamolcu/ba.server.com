function maintainListHeight(listOne, listTwo, totalItems){
    var singleItemHeight = 42;
    var margin = 20;
    var maxHeight = margin + (totalItems*singleItemHeight);
    var itemUseListHeight = $('#' + listOne).css('height');
    var itemUseListHeightVal = parseInt(itemUseListHeight.replace(/px/i, ""));
    var itemNotUseListHeight = $('#' + listTwo).css('height');
    var itemNotUseListHeightVal = parseInt(itemNotUseListHeight.replace(/px/i, ""));
    if(itemUseListHeightVal > itemNotUseListHeightVal){
        $('#' + listTwo).css('height', itemUseListHeight);
    }else if(itemUseListHeightVal < itemNotUseListHeightVal){
        $('#' + listOne).css('height', itemNotUseListHeight);
    }else{
        if(itemUseListHeightVal < maxHeight){
            itemUseListHeightVal += singleItemHeight;
            $('#' + listTwo).css('height', itemUseListHeightVal);
            $('#' + listOne).css('height', itemUseListHeightVal);
        }
    }
}