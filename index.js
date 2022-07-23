const faviconTag = document.getElementById("faviconTag");
            const logo = document.getElementById("logo");
            const isDark = window.matchMedia("(prefers-color-scheme: dark)");
            const changeFavicon = () => {
                if (isDark.matches){
                    faviconTag.href = "./img/NoDB-Dark.png";
                    logo.href = "./img/NoDB-Dark.png";
                }
                else{
                    faviconTag.href = "./img/NoDB-Light.png";
                    logo.href = "./img/NoDB-Light.png";
                }
            };
            changeFavicon();