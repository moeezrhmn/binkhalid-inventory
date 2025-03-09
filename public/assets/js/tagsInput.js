class TagInput {
    constructor(inputId) {
        this.input = document.getElementById(inputId);
        this.hiddenInput = document.createElement("input");
        this.tags = [];

        // Create container for tags and input
        this.container = document.createElement("div");
        this.container.className = "flex flex-wrap items-center gap-1 border border-gray-300 rounded-md px-2 py-1 bg-white focus-within:ring-2 focus-within:ring-indigo-300";

        // Replace the input with container
        this.input.parentNode.insertBefore(this.container, this.input);
        this.container.appendChild(this.input);
        this.input.className = "border-none focus:ring-0 p-1 outline-none w-auto text-sm bg-transparent flex-grow";

        // Hidden input field for form submission
        this.hiddenInput.type = "text";
        this.hiddenInput.classList.add('hidden');
        this.hiddenInput.name = this.input.name; // Keep original input name
        this.input.removeAttribute("name"); 
        this.input.parentElement.parentElement.appendChild(this.hiddenInput);
        

        // Event Listeners
        this.input.addEventListener("keypress", (event) => this.handleKeyPress(event));
        this.input.addEventListener("keydown", (event) => this.handleBackspace(event));

        this.handleExisitngValue();
    }

    handleKeyPress(event) {
        if (event.key === "Enter" || event.key === ",") {
            event.preventDefault();
            const tagText = this.input.value.trim().replace(/,/g, ""); // Remove commas

            if (tagText !== "" && !this.tags.includes(tagText)) {
                this.tags.push(tagText);
                this.updateTags();
            }
            this.input.value = "";
            this.input.focus();
        }
    }
    handleExisitngValue(){

        if (this.input.value != ''){
            const existingtTags = this.input.value.split(',');
            console.log(existingtTags)
           this.tags = this.tags.concat(existingtTags);
            console.log(this.tags)
            this.updateTags();
            this.input.value = "";
        }            
    }

    handleBackspace(event) {
        if (event.key === "Backspace" && this.input.value === "" && this.tags.length > 0) {
            this.removeTag(this.tags[this.tags.length - 1]); 
        }
        this.input.focus();
    }

    updateTags() {
        this.container.innerHTML = ""; // Clear previous tags
        this.tags.forEach(tag => {
            const tagElement = document.createElement("span");
            tagElement.className = "bg-indigo-100 text-indigo-700 text-xs px-2 py-1 rounded-md flex items-center gap-1";
            tagElement.innerHTML = `${tag} <button type="button" class="text-red-500 text-sm font-bold ml-1" onclick="this.closest('div').tagInputInstance.removeTag('${tag}')">×</button>`;
            this.container.appendChild(tagElement);
        });

        this.container.appendChild(this.input); // Keep input field inside container
        this.hiddenInput.value = this.tags.join(","); // Set hidden input value
        this.container.tagInputInstance = this; // Allow access for removal
    }

    removeTag(tag) {
        this.tags = this.tags.filter(t => t !== tag);
        this.updateTags();
    }
}