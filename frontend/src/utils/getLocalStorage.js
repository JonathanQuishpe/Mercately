const getLocalStorage = (key) => {
    if (!localStorage.getItem(key)) {
        return null;
    }
     
    return JSON.parse(localStorage.getItem(key));
};

export default getLocalStorage;
