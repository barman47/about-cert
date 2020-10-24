export default ({ app }, inject) => {
    inject('URLToFile', (url, filename, mimeType) => {
        return (fetch(url)
            .then(function(res){return res.arrayBuffer();})
            .then(function(buf){return new File([buf], filename,{type:mimeType});})
        );
    })
}

