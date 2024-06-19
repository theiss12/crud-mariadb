function toggleErrorMessage() {
    document.querySelector(".blocking-layer").classList.toggle("blocking-layer--closed");
}

function reloadIfSuccess(loadEvent) {
    const response = loadEvent.target.responseText;
    if (response === "Success") {
        location.reload();
    }
    else {
        toggleErrorMessage();
    }
}

function logout() {
    location.replace("./");
}

class RequestSender {
    createTable() {
        let table = document.querySelector("#new-table-name").value;
        if (table === "") return;
        const path = "./crud/create-table.php";
        const body = `table=${table}`;
        sendRequest("POST", body, path, reloadIfSuccess);
    }
    
    insertItem() {
        let message = document.querySelector("#new-item-name").value;
        message = message === "" ? message = "unknown" : message = message;
        const path = "./crud/insert-row.php";
        const body = `table=${currentTableName}&message=${message}`;
        sendRequest("POST", body, path, reloadIfSuccess);
    }
    
    updateItem() {
        const d = document;
    
        const idInput = d.querySelector("#update-id");
        const messageInput = d.querySelector("#update-message");
        const doneInput = d.querySelector("#update-done");
    
        const id = idInput.children[idInput.options.selectedIndex].value;
        const message = messageInput.value === "" ? "unknown" : messageInput.value;
        const done = doneInput.children[doneInput.options.selectedIndex].value;
        
        const path = "./crud/update-row.php";
        const body = `table=${currentTableName}&id=${id}&message=${message}&done=${done}`;
        sendRequest("POST", body, path, reloadIfSuccess);
    }
    
    deleteTable() {
        const select = document.querySelector("#delete-table-select");
        const table = select.children[select.options.selectedIndex].innerHTML;
        const path = "./crud/delete-table.php";
        const body = `table=${table}`;
        const loadHandler = table === currentTableName ? logout : reloadIfSuccess;
        sendRequest("POST", body, path, loadHandler);
    }
    
    deleteItem() {
        const select = document.querySelector("#delete-item-select");
        const id = select.children[select.options.selectedIndex].innerHTML;
        const path = "./crud/delete-row.php";
        const body = `table=${currentTableName}&id=${id}`;
        sendRequest("POST", body, path, reloadIfSuccess);
    }
    
    swapTable(inputEvent) {
        const select = inputEvent.target;
        const tableName = select.children[select.options.selectedIndex].innerHTML;
    
        if (tableName === "--CHOOSE NOTE-GROUP--") {
            return;
        }
    
        const form = document.createElement("form");
        form.method = "POST";
        form.action = "notes.php";
    
        const tableInput = document.createElement("input");
        tableInput.type = "text";
        tableInput.name = "table";
        tableInput.value = tableName;
    
        form.appendChild(tableInput);
    
        document.body.appendChild(form);
    
        form.submit();
    
        form.remove();
    }
}

function displayNoteMessageById() {
    const idInput = document.querySelector("#update-id");
    const id = idInput.children[idInput.options.selectedIndex].value;
    const targetRow = document.querySelector(`#note-${id}`);
    if (!targetRow) return;
    const message = targetRow.children[1].innerHTML;
    document.querySelector("#update-message").value = message;
}

function toggleNavigation() {
    document.querySelector("#navigation").classList.toggle("navigation--closed");
}

function toggleNavigationItem(clickEvent) {
    const clickedLabel = clickEvent.target;
    [...document.querySelectorAll(".item__headline")].forEach(label => {
        if (label.innerHTML === clickedLabel.innerHTML) return;
        label.parentElement.classList.add("navigation__item--closed")
    });
    clickedLabel.parentElement.classList.toggle("navigation__item--closed");
}

function startProgram() {
    const sender = new RequestSender();
    const d = document;
    d.querySelector("#table-name-script").remove();
    d.querySelector("#create-table-button").addEventListener("click", sender.createTable);
    d.querySelector("#insert-item-button").addEventListener("click", sender.insertItem);
    d.querySelector("#table-select").addEventListener("input", sender.swapTable);
    d.querySelector("#update-item-button").addEventListener("click", sender.updateItem);
    d.querySelector("#delete-table-button").addEventListener("click", sender.deleteTable);
    d.querySelector("#delete-item-button").addEventListener("click", sender.deleteItem);

    d.querySelector("#update-id").addEventListener("change", displayNoteMessageById);
    d.querySelector(".error-message__close-button").addEventListener("click", toggleErrorMessage);
    d.querySelector("#toggle-button").addEventListener("click", toggleNavigation);
    [...d.querySelectorAll(".item__headline")].forEach(label => label.addEventListener("click", toggleNavigationItem));
    [...d.querySelectorAll("form")].forEach(form => form.addEventListener("submit",  submitEvent=> {submitEvent.preventDefault()}));

    displayNoteMessageById();
}

window.addEventListener("load", startProgram);