console.log("product print js");



document.querySelectorAll('.sheet-description').forEach(function (container) {
    const lines = [
        ...container.querySelectorAll('.description-line')
    ];

    let lineCount = 1;
    const descriptionLineLimit = Number(
        container.dataset.descriptionLineLimit
    );
    lines.forEach(function (line,idx) {
        if(lineCount > descriptionLineLimit){
            // line.style.display = 'none';
            line.remove();
            return;
        }

        if (line.scrollWidth > line.clientWidth) {
            console.log('ဒီ line က မဆံ့ပါ:', line.textContent);
            let lineHeight = line.clientHeight;
            console.log(lineHeight);

            line.style.whiteSpace = 'normal';
            line.textContent = line.textContent.trim();

            let addLines = Math.floor(line.scrollHeight / lineHeight);
            console.log(addLines);
            lineCount += addLines;

            if(lineCount > descriptionLineLimit){
                line.style.whiteSpace = 'nowrap';
            }
        }else{
            lineCount++;
        }
    });
});