/**
 * WordPress dependencies
 */
const { ButtonGroup, Button } = wp.components;

const UnitControl = ({ selectedUnit, unitTypes, onClick }) => (
  <ButtonGroup className="eb-unit-control-btn-group">
    {unitTypes.map((unit) => (
      <Button
        className={`eb-unit-control-btn ${
          unit.value === selectedUnit && "eb-unit-active"
        }`}
        isSmall
        isPrimary={unit.value === selectedUnit}
        onClick={() => onClick(unit.value)}
      >
        {unit.label}
      </Button>
    ))}
  </ButtonGroup>
);

export default UnitControl;
