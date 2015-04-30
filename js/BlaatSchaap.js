function showElement(id) {
  console.log("showing " +id);
  document.getElementById(id).style.visibility = "visible";
  document.getElementById(id).style.display = "block";
}

function hideElement(id) {
  console.log("hiding " +id);
  document.getElementById(id).style.visibility = "hidden";
  document.getElementById(id).style.display = "none";
}

function showOnlyElement(id, elements) {
  for (var i = 0 ; i < elements.length; i++) {
    if (elements[i]==id)
      showElement(elements[i]);
    else
      hideElement(elements[i]);
  }
}

function showFirstElement(elements) {
  showOnlyElement(elements[0],elements);
}
