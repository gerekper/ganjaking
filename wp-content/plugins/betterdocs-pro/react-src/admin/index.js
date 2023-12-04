import ReactDOM from "react-dom";
import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import BetterDocsAnalytics from "./Analytics";

const queryClient = new QueryClient();

ReactDOM.render(
    <QueryClientProvider client={queryClient}>
        <BetterDocsAnalytics />
    </QueryClientProvider>,
    document.getElementById("betterdocsAnalytics")
);
