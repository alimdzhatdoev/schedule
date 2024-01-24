function changeTab(clickedElement, tabName) {
    var elements = document.querySelectorAll('.bottomMenu_elem');
    elements.forEach(function (element) {
        element.classList.remove('activeBottomElem', 'roundedTopRight', 'roundedTopLeft');
    });

    clickedElement.classList.add('activeBottomElem');

    var index = Array.from(elements).indexOf(clickedElement);
    if (index === 0) {
        clickedElement.classList.add('roundedTopRight');
    } else if (index === elements.length - 1) {
        clickedElement.classList.add('roundedTopLeft');
    } else {
        clickedElement.classList.add('roundedTopRight', 'roundedTopLeft');
    }

    var tabs = document.querySelectorAll('.tabSchedule, .tabProfile');
    tabs.forEach(function (tab) {
        tab.classList.remove('tabShow');
    });

    var activeTab = document.querySelector('.' + tabName);
    activeTab.classList.add('tabShow');
}
