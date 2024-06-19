function sendRequest(method, body, path, loadHandler) {
    const request = new XMLHttpRequest();
    request.open(method, path);
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    request.onload = loadHandler;
    request.send(body);
}