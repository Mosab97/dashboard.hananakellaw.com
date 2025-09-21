import FormBuilderCore from "./core/FormBuilderCore";
import QuestionManager from "./core/QuestionManager";
import OptionHandler from "./components/OptionHandler";
import PreviewManager from "./components/PreviewManager";

class FormBuilderInitializer {
    static init(appData) {
        const { programId, fieldTypes, routes } = appData;

        const optionHandler = new OptionHandler(fieldTypes);
        const questionManager = new QuestionManager(optionHandler);
        const previewManager = new PreviewManager(programId, questionManager);

        const formBuilder = new FormBuilderCore(
            programId,
            questionManager,
            previewManager
        );

        return formBuilder;
    }
}

// Initialize when document is ready
jQuery(document).ready(() => {
    if (!window.appData) {
        console.error("Application data not found");
        return;
    }

    const formBuilder = FormBuilderInitializer.init(window.appData);
});

export default FormBuilderInitializer;
