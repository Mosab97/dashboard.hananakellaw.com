class RoutesManager {
    constructor(routes, programId) {
        this.routes = routes;
        this.programId = programId;
    }

    getUrl(routeName, params = {}) {
        let url = this.routes[routeName];

        if (!url) {
            console.error(`Route '${routeName}' not found`);
            return "";
        }

        try {
            // Replace programId placeholder
            url = url.replace(":programId", this.programId);

            // Replace other parameters if they exist and are not null
            Object.keys(params).forEach((key) => {
                if (params[key] !== null && params[key] !== undefined) {
                    url = url.replace(`:${key}`, params[key]);
                }
            });

            return url;
        } catch (error) {
            console.error(
                `Error generating URL for route '${routeName}':`,
                error
            );
            return "";
        }
    }
}

export default RoutesManager;
