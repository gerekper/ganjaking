export {
  generateBackgroundControlStyles,
  generateBackgroundAttributes,
} from "./backgroundHelpers";

export {
  generateTypographyAttributes,
  generateTypographyStyles,
} from "./typoHelpers";

export {
  generateDimensionsAttributes,
  generateDimensionsControlStyles,
} from "./dimensionHelpers";

export {
  generateBorderShadowAttributes,
  generateBorderShadowStyles,
} from "./borderShadowHelpers";

export {
  generateResponsiveRangeStyles,
  generateResponsiveRangeAttributes,
} from "./responsiveRangeHelpers";

export {
  textInsideForEdit,
  generateRandomNumber,
  hardMinifyCssStrings,
  softMinifyCssStrings,
  isCssExists,
} from "./miniHelperFuncs";

export {
  handleDesktopBtnClick,
  handleTabBtnClick,
  handleMobileBtnClick,
} from "./handlingPreviewBtnsHelpers";

export {
  mimmikCssForResBtns,
  mimmikCssForPreviewBtnClick,
  mimmikCssOnPreviewBtnClickWhileBlockSelected,
  duplicateBlockIdFix,
} from "./funcsForUseEffect";

export { getFlipTransform, getButtonClasses } from "./flipboxHelpers";

export { stripHTML } from "./stripeHTML";

//
// // unused function
// export const getBackgroundImage = (type, gradientValue, imageURL) => {
//   switch (type) {
//     case "fill":
//       return "none";

//     case "gradient":
//       return gradientValue;

//     case "image":
//       if (imageURL) {
//         return `url(${imageURL})`;
//       }
//       return "none";
//   }
// };
