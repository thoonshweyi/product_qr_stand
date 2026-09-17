console.log("product print js");

document.querySelectorAll('.sheet-description').forEach(function (container) {

    const sourceLines = [
        ...container.querySelectorAll('.description-source-line')
    ];

    // container.innerHTML = '';

    let lineCount = 0;
    const descriptionLineLimit = Number(
        container.dataset.descriptionLineLimit
    );
    // console.log(descriptionLineLimit);
    for (const sourceLine of sourceLines) {
        console.log(sourceLine);

        const line = document.createElement('div');

        line.className = 'description-line';
        line.textContent = sourceLine.textContent.trim();
        line.style.whiteSpace = 'normal';

        console.log(sourceLine.scrollWidth , sourceLine.clientWidth);
        if (sourceLine.scrollWidth > sourceLine.clientWidth) {
            console.log('ဒီ line က မဆံ့ပါ:', sourceLine.textContent);
            lineCount += 1;
        }
        lineCount += 1;

        if(lineCount <= descriptionLineLimit){
            container.appendChild(line);
        }
    }
});